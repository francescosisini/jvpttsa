# Todo
- [ ] upload DICOM file from browser
- - [x]  sudo chown www-data jvp/uploads/
- - [x] Big file can't be uploaded:
    Resolved: changing 
    Maximum allowed size for uploaded files.                                                         
    http://php.net/upload-max-filesize                                                               
    upload_max_filesize = 50M
    in /etc/php/7.2/apache2/php.ini 
- - [ ]  needs uploaded notification
- [ ] POST jvp orphan-data from imageJ 
- [ ] orphan-data reconciliation
