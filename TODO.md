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
- [X] JSON Api using same URL with different extension, default remain HTML as it is now
- [ ] give more info about images dimension - see Google Drive API doc
- [ ] Give complete folders list tree as a JSON
- [X] OAuth2 client
- [X] Use Google paginated result & pagination token system
- [ ] Build a clickable list of available mimeTypes filters: pdf, mp4, docx, png, and grouped filters : docs, videos, images, ...
- [ ] Building browsable folders treeview, sarting from the `root` typed folder
- [ ] JSON: most downloaded docs using Google API
- [ ] JSON: last uploaded docs using Google API
- [X] predictable API-like URLs, even for HTML front
- [ ] Front-end design


## v1.2
- [ ] [logstash + kibana to monitore search queries & results](https://coderwall.com/p/irhi_q/how-to-use-logstash-with-monolog)
- [ ] Caching the one-hour-valid token ?
- [ ] Special private folder
- [ ] better search experience : "did you mean ?"
- [ ] better search experience : facets results

## Notable Google Drive API Features

A selection of suitable feats for this project: See https://developers.google.com/drive/v2/reference/

- Files::get - Gets a file's metadata by ID.
- Files::list - Lists the user's files
- About::get - Gets the information about the current user along with Drive API settings
- Changes::list - Lists the changes for a user
- Children::list - Lists a folder's children. To list all children of the root folder, use the alias root for the folderId value
- Parents::list - Lists a (folder) file's parents
- Properties:list - Lists a (folder) file's properties
