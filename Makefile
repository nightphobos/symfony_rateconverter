build:
	docker build -t symfony_rateconverter:0.0.1 -t symfony_rateconverter:latest .
sh:
	docker run --rm -it --mount type=bind,src=${PWD},dst=/app symfony_rateconverter /bin/sh