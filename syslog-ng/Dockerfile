FROM balabit/syslog-ng:latest

COPY syslog-ng.conf /etc/syslog-ng/syslog-ng.conf

EXPOSE 514/udp
EXPOSE 601

WORKDIR /etc/syslog-ng

CMD ["-edv"]