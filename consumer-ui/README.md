## VMS development enviornment setup instructions

1. Create a new [Virtual Machine](http://vms.codeforeauclaire.org/) >> SSH in >> Run (or read) vms quick install
 1. `curl -L -o- https://rawgit.com/codeforeauclaire/foodtrucks/master/consumer-ui/bin/vmsquickinstall.sh | bash`
1. Run app
 1. `(cd ~/foodtrucks/consumer-ui && ./bin/serve)`
 1. Load http://{vms-ip}:3000 in your browser
 1. Edit a file >> restart ./bin/serve >> see changes in your browser

## Development Notes

* Mapping via Leaflet http://leafletjs.com/
 * http://leafletjs.com/examples/mobile.html
 * Data: Static json data that will run
* UI Components via JQuery Mobile
* Hosting - Just client side. Use apache, use python, use whatever. Here are some quick methods
 * https://askubuntu.com/questions/377389/how-to-easily-start-a-webserver-in-any-folder
* [Original draw.io mockup from mar 28th 2016 C4EC hack night](https://drive.google.com/file/d/0B1hUzWEXfF7oWHVPRGZhLVE3UDA/view)
