[![js-standard-style](https://img.shields.io/badge/code%20style-standard-brightgreen.svg)](http://standardjs.com)

# Food Trucks :: Consumer UI

Displays where food trucks so consumers can get some food.

## VMS Easy setup development environment

Use these instructions to setup a temporary* development environment of this project.

1. Fork this repository
1. Create a new [Virtual Machine](http://vms.codeforeauclaire.org/) >> SSH in >> Run vms quick install:
    1. `export GHUSER='AnthonyAstige'` (Replace AnthonyAstige with your username)
    1. `curl -L -o- https://rawgit.com/$GHUSER/foodtrucks/master/consumer-ui/bin/vmsquickinstall.sh | bash`
1. Run app
    1. `(cd ~/foodtrucks/consumer-ui && ./bin/serve)`
    1. Load http://{vms-ip}:3000 in your browser
    1. Edit a file >> restart ./bin/serve >> see changes in your browser

*For a permanent development environment we recommend you read the referenced script above to install locally.

## Development Notes

### Misc

* Don't work directly on the gh-pages branch
    - `git subtree push --prefix=consumer-ui https://github.com/codeforeauclaire/foodtrucks.git gh-pages`
* Mapping via Leaflet http://leafletjs.com/
    * http://leafletjs.com/examples/mobile.html
    * Data: Static json data that will run
* UI Components via JQuery Mobile
* [Original draw.io mockup from mar 28th 2016 C4EC hack night](https://drive.google.com/file/d/0B1hUzWEXfF7oWHVPRGZhLVE3UDA/view)
* [General Specs](../SPECS.md)
