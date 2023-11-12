package main

func main() {
	go startHTTPServer()
	startUDPServer()
}
