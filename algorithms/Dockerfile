FROM python:3.11-bullseye

RUN pip install --upgrade pip
RUN pip install flask numpy

WORKDIR /usr/src/

COPY . .

CMD [ "python", "main.py" ]