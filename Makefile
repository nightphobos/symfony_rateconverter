build:
	docker build -t symfony_rateconverter:0.0.1 -t symfony_rateconverter:latest .

install:
	docker run --rm --mount type=bind,src=${PWD},dst=/app -w /app symfony_rateconverter composer install

sh:
	docker run --rm -it --mount type=bind,src=${PWD},dst=/app -w /app symfony_rateconverter /bin/sh

get:
	docker run --rm --mount type=bind,src=${PWD},dst=/app -w /app symfony_rateconverter bin/console app:rates:get

convert:
	docker run --rm --mount type=bind,src=${PWD},dst=/app -w /app symfony_rateconverter bin/console app:rates:convert BTC NZD 10