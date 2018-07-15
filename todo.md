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
- [ ] POST jvp orphan-data from imageJ
- - [ ] Implements controller action
- [ ] orphan-data reconciliation
