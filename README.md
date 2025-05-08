# musicsync
A Community for Sharing and Viewing Music Sheets

1. Pages (≥ 4 Pages)
Home Page: Show featured and recent sheets
Sheet Detail Page: View a sheet, add comments/bookmarks
Upload/Edit Sheet Page: Users can upload sheets (multiple images) and edit them
My Sheets: List of the user’s uploaded sheets (with edit/delete options)
Favorites Page: Bookmarked sheets with timestamps
Preview Mode (Fullscreen View): For immersive music sheet viewing

2. POST Routes (≥ 3 POST Routes)
/sheets/upload – Upload a new music sheet
/comments – Add/edit/delete a comment
/favorites/add – Add/remove a sheet to/from favorites

3. Create, Edit, Delete Functionality
Upload sheets (create)
Edit sheet info (edit)
Delete sheet
Add/edit/delete user comments
Add/remove favorites

4. Validation 
Use Laravel’s built-in validate() method for:
Sheet upload (title required, images required?, file type validation)
Comment submission (non-empty, max length)
Profile info (if applicable)

Show field-specific error messages using Bootstrap's validation UI.

