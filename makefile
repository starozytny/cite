git:
	git add .
	git commit -m "$m"

route:
	php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json