# Food Trucks: Wireframe proposal

## Consumer UI

1. Full page map displaying food truck icons
    1. Click on to bubble up quick info (Name, schedule, ...)
1. Control overlay for current day

[Awaiting sketch from Aaron Harder](https://trello.com/c/fCrOYzL8/4-share-draw-io-graphic-mock-up)

## Vendor UI

1. Edit truck info
1. Add schedule
1. Activate now & here / de-activate

(Could use a sketch)

## Technology

1. Google maps JavaScript API
1. Support on desktop web & mobile web
    1. Optional: Android & iOS apps
1. Easy & popular (amongst devs that take this up) database and server side backend

## Admin UI / database structure

Quickly thrown together perhaps with auto-form packages. This will only display to whomever manages the site.

**Trucks**

1. Name
1. Description
1. Phone
1. Links (array of urls)
1. Icon
    1. File upload, hack storage in database it's self so we don't have to deal with file system

**Schedules**

1. TruckId
1. Date
1. timeOpen
1. timeClose
1. lat
1. lng

# References

* [Code for Eau Clare - Eau Hack Night - #4 - Let's Map our Food Trucks!](https://docs.google.com/document/d/1RBFx_S-Z7D7GeBGQFHzdSiD4s_yqPjvIQlwUWH80OEQ/edit#)
