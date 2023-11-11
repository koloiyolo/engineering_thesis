FROM rust:alpine3.17

# RUN apt-get update -y
# RUN apt-get install cargo -y

WORKDIR /usr/src/server
COPY . .

RUN cargo build --release
RUN cargo install --path .

CMD ["listening_server"]