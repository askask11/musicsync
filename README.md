# musicsync
A Community for Sharing and Viewing Music Sheets

Video Demo: https://drive.google.com/file/d/19JPs4WpagDyouCC-PyoSg9c70myimKGa/view?usp=drive_link

1. Pages (≥ 4 Pages)
Home Page: Show featured and recent sheets
Sheet Detail Page: View a sheet, add comments/bookmarks
Upload/Edit Sheet Page: Users can upload sheets (multiple images) and edit them
My Sheets: List of the user’s uploaded sheets (with edit/delete options)
Favorites Page: Bookmarked sheets with timestamps
Preview Mode (Fullscreen View) - TBD : View sheet in fullscreen mode

2. POST Routes (≥ 3 POST Routes)

POST /upload: Handle file uploads
POST /sheets/{sheet}/comments : Add a comment to a sheet
POST /sheets/create : Create a new sheet

3. Create, Edit, Delete Functionality
Upload sheets (create)
Edit sheet info (edit)
Delete sheet
Add/edit/delete user comments
Add/remove favorites

4. Validation 
Use Laravel’s built-in validate() method for:
Sheet upload (title required, images required, file type validation)
Comment submission (non-empty, max length 1000)
Profile info - email, password need to be validated when registering

Show field-specific error messages using Bootstrap's validation UI.

