# Todo
- [x] upload DICOM file from browser
- - [x]  sudo chown www-data jvp/uploads/; sudo chown www-data studies/
- - [x] Big file can't be uploaded:
    Resolved: changing 
    Maximum allowed size for uploaded files.                                                         
    http://php.net/upload-max-filesize                                                               
    upload_max_filesize = 50M
    post_max_size =	0
    in /etc/php/7.2/apache2/php.ini 
- - [x]  needs uploaded notification
- [x] POST jvp orphan-data from imageJ
- - [x] Implements controller action
- - [x] orphan-data reconciliation
- - [x] Create header for orphan-data with no reconcicliation DICOM Study
- - - [ ] Add 
- [x] OnLine CSA measurements
- - [x] KeyPress event detection
- - [x] JVP dataset recording
- - [x] HTTP data transmission
- - [x] User confirm Upload. User complete upload form.
- - - [x] add selection for Patient ID o Study ID (for reconciliation)
- - - [x] add  Left/Right
- - - [x] add  j123 position
- - - [x] add  pixel/cm (remove from initial dialog)
- - [x] Pause aquisition to make measurament on the image
- - [x] Set acquisition parameter
- - [x] Set JVP and Plot windows in given location in the window
- - [ ] Set the focus in JVP plot after the Plot is shown
