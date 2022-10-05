FROM docker.elastic.co/elasticsearch/elasticsearch:7.15.1

WORKDIR /usr/share/elasticsearch

# Install ES plugins
RUN bin/elasticsearch-plugin install analysis-phonetic
RUN bin/elasticsearch-plugin install analysis-icu
