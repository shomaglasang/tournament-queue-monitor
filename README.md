# Tournament Queue Monitor for Jiu-Jitsu, Wrestling and other match-based sports

## Features
1. Uses Google sheet as the database for the list of matches.
2. Display the list of matches in a simple web page (e.g. monitor).
3. Toggle matches that are shown on the monitor.
4. Changes on the Google sheet are automatically reflected on the monitor.
5. Minimal web page implementation. Runs on any web server.

## Google Sheet ID and Sheet Name
Open the file `assets/js/app/app.js` and set the variable `sheetId` to the Google Sheet ID.
Example Google Sheet URL: `https://docs.google.com/spreadsheets/d/1sLCgW9klZLdE0A3XaWIf7E4oLzSIE1CLCNgz5Labcde/edit#gid=136282605`
```
const sheetId = '1sLCgW9klZLdE0A3XaWIf7E4oLzSIE1CLCNgz5Labcde';
```
In your Google Sheet, set the name of the sheet into `Matchlist`.

## Google Sheet Matchlist

## Queue Monitor
