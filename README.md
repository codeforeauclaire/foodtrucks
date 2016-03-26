# Food Trucks: Wireframe proposal

## Front-end UI

1. Full page map with food trucks
1. Control overlay for days of the week (checkbox for each)
    1. Loading defaults to current day checked only

## Technology

1. Google maps JavaScript API
1. Support on desktop web, mobile web, android, and iOS
1. Easy & popular (amongst devs that take this up) database and server side backend

## Admin UI / database structure

Quickly thrown together perhaps with auto-form packages. This will only display to whomever manages the site.

**Trucks**

1. Name
1. Description
1. Phone
1. Links (array of urls)
1. Image
    1. File upload, hack storage in database it's self so we don't have to deal with file system

**Schedules**

1. TruckId
1. DaysOfWeek
    * Sunday
    * Monday
    * Tuesday
    * Wednesday
    * Thursday
    * Friday
    * Saturday
1. lat
1. lng

# References

[Original Google Doc](https://docs.google.com/document/d/1RBFx_S-Z7D7GeBGQFHzdSiD4s_yqPjvIQlwUWH80OEQ/edit#)
