/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
   config.filebrowserBrowseUrl = base_url + '/kcfinder/browse.php?type=files';
   config.filebrowserImageBrowseUrl = base_url + '/kcfinder/browse.php?type=images';
   config.filebrowserFlashBrowseUrl = base_url + '/kcfinder/browse.php?type=flash';
   config.filebrowserUploadUrl = base_url + '/kcfinder/upload.php?type=files';
   config.filebrowserImageUploadUrl = base_url + '/kcfinder/upload.php?type=images';
   config.filebrowserFlashUploadUrl = base_url + '/kcfinder/upload.php?type=flash';
};
