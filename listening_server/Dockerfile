FROM golang:1.21

WORKDIR /usr/src/udp_server

COPY go.mod .

RUN go mod download

COPY . .
RUN go build -v -o /usr/local/bin/udp_server

EXPOSE 514/udp
EXPOSE 3000


COPY wait-for-rabbit.sh .
RUN chmod +x /app/wait-for-rabbit.sh

# Command to start your service with the wait script
CMD ["./wait-for-rabbit.sh", "udp_server"]
