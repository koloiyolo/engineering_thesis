FROM debian:latest

RUN apt-get update -y
RUN apt-get install cargo -y

WORKDIR /usr/src/server
EXPOSE 514/udp
COPY . .

RUN cargo build --release
RUN cargo install --path .

CMD ["target/release/listening_server"]
