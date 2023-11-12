package main

import (
	"fmt"
	"log"
	"net"
	"time"
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
		for isCollectingLogs {
			n, addr, err := pc.ReadFrom(buf)
			if err != nil {
				log.Fatal(err)
			}
			msg := string(buf[:n])
			fmt.Printf("Received UDP message from %s: %s\n", addr, msg)

			//todo rabbit implementation
		}

		fmt.Println("Log collection is currently stopped. Waiting for reactivation...")
		time.Sleep(1 * time.Second)
	}
}
