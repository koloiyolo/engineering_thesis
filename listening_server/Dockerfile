FROM golang:1.21

RUN apt-get update && apt-get install -y netcat-openbsd

WORKDIR /usr/src/udp_server

COPY go.mod .

RUN go mod download

COPY . .
RUN go build -v -o /usr/local/bin/udp_server

EXPOSE 514/udp
EXPOSE 3000


COPY wait_for_rabbit.sh /app/wait_for_rabbit.sh
RUN chmod +x /app/wait_for_rabbit.sh

# Command to start your service with the wait script
CMD ["/app/wait_for_rabbit.sh", "udp_server"]
