## VMS development enviornment setup instructions

1. Create a new Digital Ocean Ubuntu 14.04.4 machine >> SSH in >> Run (or read) vms quick install
 1. `curl -L -o- https://rawgit.com/codeforeauclaire/foodtrucks/master/foodtrucks-consumer-ui/bin/vmsquickinstall.sh | bash`
1. Run app
 1. `(cd ~/foodtrucks/api && ./bin/serve)`
 1. Load http://{vms-ip} in your browser
 1. Edit a file to see changes in your browser instantly

## Development Notes

* Mapping via Leaflet http://leafletjs.com/
 * http://leafletjs.com/examples/mobile.html
 * Data: Static json data that will run
* UI Components via JQuery Mobile

* Hosting - Just local stuff.  Use apache, use python, use whatever.  Here are some quick methods
 * https://askubuntu.com/questions/377389/how-to-easily-start-a-webserver-in-any-folder
