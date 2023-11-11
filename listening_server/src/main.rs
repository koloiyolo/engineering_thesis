use std::net::{UdpSocket};

fn main() {
    let udp_socket = UdpSocket::bind("0.0.0.0:514").expect("Could not bind to address");
    println!("Server started on port 514");

    let mut buf = [0u8; 1024];
    loop {
        match udp_socket.recv_from(&mut buf) {
            Ok((size, src)) => {
                let received = &buf[..size];
                if let Ok(msg) = std::str::from_utf8(received) {
                    println!("Received from {}: {}", src, msg);
                    // Handle the received message here
                } else {
                    println!("Received non-UTF8 data from {}", src);
                }
            }
            Err(e) => {
                eprintln!("Error receiving data: {}", e);
            }
        }
    }
}
