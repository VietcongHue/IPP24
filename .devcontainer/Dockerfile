FROM mcr.microsoft.com/devcontainers/php:8.3

RUN sed -i '/cs_CZ.UTF-8/s/^# //g' /etc/locale.gen && \
    locale-gen
ENV LANG cs_CZ.UTF-8
ENV LC_ALL cs_CZ.UTF-8

RUN apt-get update && apt-get install -y \
    dos2unix \
    && rm -rf /var/lib/apt/lists/*
