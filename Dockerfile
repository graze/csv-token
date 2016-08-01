FROM graze/stats:7.0

RUN apk add --no-cache --repository "http://dl-cdn.alpinelinux.org/alpine/edge/testing" \
    php7-xdebug

ADD . /opt/graze/csv-token

WORKDIR /opt/graze/csv-token

CMD /bin/sh
