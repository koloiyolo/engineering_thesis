package main

import (
	"fmt"
	"log"
	"net/http"
)

var isCollectingLogs = true

func handleRequest(w http.ResponseWriter, r *http.Request) {
	if r.Method == "POST" && r.URL.Path == "/control-logs" {
		body := make([]byte, r.ContentLength)
		_, err := r.Body.Read(body)
		if err != nil {
			http.Error(w, "Error reading request body", http.StatusBadRequest)
			return
		}
		data := string(body)
		if data == "false" {
			isCollectingLogs = false
			fmt.Fprintf(w, "Log collection stopped")
		} else if data == "true" {
			isCollectingLogs = true
			fmt.Fprintf(w, "Log collection started")
		} else {
			http.Error(w, "Invalid request body", http.StatusBadRequest)
		}
	} else {
		http.Error(w, "Invalid endpoint", http.StatusNotFound)
	}
}

func startHTTPServer() {
	http.HandleFunc("/control-logs", handleRequest)
	fmt.Println("HTTP server started on port 3000")
	if err := http.ListenAndServe(":3000", nil); err != nil {
		log.Fatal(err)
	}
}
