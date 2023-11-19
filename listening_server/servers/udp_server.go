package servers

import (
	"context"
	"fmt"
	"log"
	"net"
	"time"

	amqp "github.com/rabbitmq/amqp091-go"
)

func failOnError(err error, msg string) {
	if err != nil {
		log.Panicf("%s: %s", msg, err)
	}
}

func StartUDPServer() {
	//rabbitmq conn init

	conn, err := amqp.Dial("amqp://guest:guest@rabbit:5672/")
	failOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	q, err := ch.QueueDeclare(
		"logs", // name
		false,  // durable
		false,  // delete when unused
		false,  // exclusive
		false,  // no-wait
		nil,    // arguments
	)
	failOnError(err, "Failed to declare a queue")

	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	//udp socket init
	pc, err := net.ListenPacket("udp", "0.0.0.0:514")
	failOnError(err, "Failed to initialize UDP socket")
	defer pc.Close()

	fmt.Println("UDP server started on port 514")

	buf := make([]byte, 1024)
	for {
		for isCollectingLogs {
			n, addr, err := pc.ReadFrom(buf)
			failOnError(err, "Failed to read message from channel")
			msg := string(buf[:n])
			fmt.Printf("Received UDP message from %s: %s\n", addr, msg)

			body := msg
			err = ch.PublishWithContext(ctx,
				"",     // exchange
				q.Name, // routing key
				false,  // mandatory
				false,  // immediate
				amqp.Publishing{
					ContentType: "text/plain",
					Body:        []byte(body),
				})
			failOnError(err, "Failed to publish a message")
			log.Printf("Sent %s to rabbitmq\n", body)
		}

		fmt.Println("Log collection is currently stopped. Waiting for reactivation...")
		time.Sleep(1 * time.Second)
	}
}
