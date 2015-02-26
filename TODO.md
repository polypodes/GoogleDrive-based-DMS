# TODO

## v1.0

- [X] No admin, no login required for end user: all is public & read-only by default
- [X] Large file sharing
- [X] End user can view + download without Google Account Authentication 
- [X] Stats via Google Analytics
- [X] Full-text Search inside documents + simple results list
- [X] Determine the `root` folder (see 'Root folder ID' in `->about()`)
- [X] Simple Bootstrap Theme
- [X] Tests with medias

## v1.1
- [x] GET request for search route
- [X] API: Have an API
- [X] API: propose a [RAML](http://raml.org) API definition & generate the API documentation
- [X] Web/API: OAuth2 client
- [X] Web/API: Use Google paginated result & pagination token system
- [X] Web/API: Build a clickable list of available mimeTypes filters: pdf, mp4, docx, png, and grouped filters : docs, videos, images, ...
- [X] Web/API: Building browsable folders navigation menu starting from the `root` typed folder
- [X] API: last uploaded docs using Google API
- [X] predictable API-like URLs, even for HTML front
- [X] Front-end design


## v1.2
- [ ] API: most downloaded docs using Google API
- [ ] [logstash + kibana to monitore search queries & results](https://coderwall.com/p/irhi_q/how-to-use-logstash-with-monolog)
- [ ] Special private folder
- [ ] Web/API: better search experience : "did you mean ?"
- [ ] Web/API: better search experience : facets results

## Notable Google Drive API Features

A selection of suitable feats for this project: See https://developers.google.com/drive/v2/reference/

- Files::get - Gets a file's metadata by ID.
- Files::list - Lists the user's files
- About::get - Gets the information about the current user along with Drive API settings
- Changes::list - Lists the changes for a user
- Children::list - Lists a folder's children. To list all children of the root folder, use the alias root for the folderId value
- Parents::list - Lists a (folder) file's parents
- Properties:list - Lists a (folder) file's properties
