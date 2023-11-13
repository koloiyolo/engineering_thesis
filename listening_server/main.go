package main

import (
	"listening_server/servers"
)

func main() {
	go servers.StartHTTPServer()
	servers.StartUDPServer()
}
