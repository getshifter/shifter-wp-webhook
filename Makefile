list:
	find . -type f -name '*.php' -print > files
	find ./libs/ -type f -name '*.js' -print >> files
	find ./languages/ -type f -name '*.pot' -print >> files

pkg: clean
	tar -cvzf shifter-wp-webhook.tgz -T files

clean:
	rm -f shifter-wp-webhook.tgz

.PHONY: list pkg clean
