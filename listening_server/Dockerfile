FROM golang:1.21

WORKDIR /usr/src/udp_server

COPY go.mod .

RUN go mod download

COPY . .
RUN go build -v -o /usr/local/bin/udp_server

EXPOSE 514/udp
EXPOSE 3000

CMD ["udp_server"]