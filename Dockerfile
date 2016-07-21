FROM graze/stats:7.0

ADD . /opt/graze/csv-token

WORKDIR /opt/graze/csv-token

CMD /bin/sh
