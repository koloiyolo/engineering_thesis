package main

import (
	"fmt"
	"log"
	"net"
)

func startUDPServer() {
	pc, err := net.ListenPacket("udp", "0.0.0.0:514")
	if err != nil {
		log.Fatal(err)
	}
	defer pc.Close()

	fmt.Println("UDP server started on port 514")

	buf := make([]byte, 1024)
	for {
		n, addr, err := pc.ReadFrom(buf)
		if err != nil {
			log.Fatal(err)
		}

		msg := string(buf[:n])
		fmt.Printf("Received UDP message from %s: %s\n", addr, msg)
		// Additional logic to handle the UDP message, e.g., send to RabbitMQ
	}
}
