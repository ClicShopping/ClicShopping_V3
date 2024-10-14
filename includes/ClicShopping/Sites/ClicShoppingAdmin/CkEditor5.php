<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Sites\ClicShoppingAdmin;

use ClicShopping\OM\CLICSHOPPING;
use ClicShopping\OM\HTML;
use ClicShopping\OM\HTTP;
use ClicShopping\OM\Registry;
use DOMDocument;
use DOMXPath;
use function is_null;
use function is_string;

class CkEditor5 extends HTML
{
  /**
   * @return string
   */
  public static function getWysiwyg(): string
  {
    $output = self::getJsCkEditor();
    $output .= self::getJsElFinder();

    return $output;
  }

  /**
   * @return string
   */
  public static function getJsCkEditor(): string
  {
    $code = self::getWysiwygLanguage();

    $script = '<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script><br />' . "\n";
    // $script .= Gpt::gptCkeditorParameters();

    if ($code != 'en') {
      if (!empty($code)) {
        $script = '<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script><br />' . "\n";
      }
    }

    return $script;
  }

  /**
   * @return string
   */
  public static function getJsElFinder(): string
  {
    $script = '
            <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
            <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.min.css"/>
            <link rel="stylesheet" type="text/css" href="' . HTTP::getShopUrlDomain() . 'ext/elFinder-master/css/elfinder.min.css"/>
            <link rel="stylesheet" type="text/css" href="' . HTTP::getShopUrlDomain() . 'ext/elFinder-master/css/theme.css"/>
        ';
    $script .= '<script src="' . HTTP::getShopUrlDomain() . 'ext/elFinder-master/js/elfinder.min.js"></script>' . "\n";

    return $script;
  }


  /**
   * @return string
   */
  public static function getWysiwygLanguage(): string
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $code = $CLICSHOPPING_Language->getCode();

    if (empty($code)) {
      $code = DEFAULT_LANGUAGE;
    }

    return $code;
  }

  /**
   * @param string $name
   * @return string
   */
  public static function getWysiwygId(string $name): string
  {
    $result = str_replace('[', '', $name);
    $result = str_replace(']', '', $result);

    return $result;
  }

  /**
   * @return string
   */
  public static function getElFinderConnector(): string
  {
    $connector = HTTP::getShopUrlDomain() . 'ext/elFinder-master/php/connector.minimal.php';

    return $connector;
  }

/**
* Outputs a form textarea field with ckeditor
* @param string $name
* @param string|null $value
* @param int|null $width
* @param int|null $height
* @param string|null $text
* @param string|null $parameters
* @param bool $override
* @return string
   */
  public static function textAreaCkeditor(string $name, string|null $value = null, mixed $width = 750, mixed $height = 200, string|null $text = null, string|null $parameters = null, bool $override = true): string
  {
    $ckeditor_id = str_replace('[', '', $name);
    $ckeditor_id = str_replace(']', '', $ckeditor_id);
    $connector = static::getElFinderConnector();
    $language_code = static::getWysiwygLanguage();

    $field = '<textarea name="' . $name . '"  id="' . $ckeditor_id . '"';

    if (!is_null($parameters)) {
      $field .= ' ' . $parameters;
    }

    $field .= ' />';

    if (($override === true) && ((isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])))) {
      if (isset($_GET[$name]) && is_string($_GET[$name])) {
        $field .= HTML::outputProtected($_GET[$name]);
      } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
        $field .= HTML::outputProtected($_POST[$name]);
      }
    } elseif (!is_null($text)) {
      $field .= HTML::outputProtected($text);
    }

    $field .= '</textarea>';

//elfinder connector declaration
    $field .= "<script async>
              // elfinder folder hash of the destination folder to be uploaded in this CKeditor 5
              const uploadTargetHash{$ckeditor_id} = 'l2_Q0stRmlsZXM_{$ckeditor_id}';
              // elFinder connector URL
              //const connectorUrl = 'php/connector.minimal.php';
              const connectorUrl{$ckeditor_id} = '{$connector}';

              ClassicEditor
              .create(document.querySelector('#{$ckeditor_id}'), { 
                language: '{$language_code}',
                
                 toolbar: {
                          items: [
                              'heading', '|',
                              'bold', 'italic', '|',
                              'bulletedList', 'numberedList', '|',
                              'outdent', 'indent', '|',
                              'undo', 'redo',
                              '|',
                              'link', 'ckfinder', 'blockQuote', 'insertTable', 'mediaEmbed', '|',                             
                          ],
                          shouldNotGroupWhenFull: true
                        },
          
                         heading: {
                            options: [
                                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                            ]
                         },
               } )

          //elfinder
           .then(editor => {
              const ckf = editor.commands.get('ckfinder'),
                fileRepo = editor.plugins.get('FileRepository'),
                ntf = editor.plugins.get('Notification'),
                i18 = editor.locale.t,
                // Insert images to editor window
                insertImages = urls => {
                  const imgCmd = editor.commands.get('imageUpload');
                  if (!imgCmd.isEnabled) {
                      ntf.showWarning(i18('Could not insert image at the current position.'), {
                          title: i18('Inserting image failed'),
                          namespace: 'ckfinder'
                      });
                return;
                }
                  editor.execute('imageInsert', { source: urls });
              },
              // To get elFinder instance
              getfm = open => {
              return new Promise((resolve, reject) => {
                // Execute when the elFinder instance is created
                const done = () => {
                  if (open) {
                    // request to open folder specify
                    if (!Object.keys(_fm.files()).length) {
                      // when initial request
                      _fm.one('open', () => {
                        _fm.file(open)? resolve(_fm) : reject(_fm, 'errFolderNotFound');
                      });
                                      } else {
                      // elFinder has already been initialized
                      new Promise((res, rej) => {
                        if (_fm.file(open)) {
                          res();
                        } else {
                          // To acquire target folder information
                          _fm.request({cmd: 'parents', target: open}).done(e =>{
                            _fm.file(open)? res() : rej();
                          }).fail(() => {
                            rej();
                          });
                                              }
                      }).then(() => {
                        // Open folder after folder information is acquired
                        _fm.exec('open', open).done(() => {
                          resolve(_fm);
                        }).fail(err => {
                          reject(_fm, err? err : 'errFolderNotFound');
                        });
                                          }).catch((err) => {
                        reject(_fm, err? err : 'errFolderNotFound');
                      });
                    }
                  } else {
                    // show elFinder manager only
                    resolve(_fm);
                  }
                };
      
                // Check elFinder instance
                if (_fm) {
                  // elFinder instance has already been created
                  done();
                } else {
                  // To create elFinder instance
                  $.dialogelfinder = jQuery.dialogelfinder;
                  _fm = $('<div/>').dialogelfinder({
                        // dialog title
                        title : 'File Manager',
                        // connector URL
                        url : connectorUrl{$ckeditor_id},
                        // start folder setting
                        startPathHash : open? open : void(0),
                        // Set to do not use browser history to un-use location.hash
                        useBrowserHistory : false,
                        // Disable auto open
                        autoOpen : false,
                        // elFinder dialog width
                        width : '80%',
                        // set getfile command options
                        commandsOptions : {
                    getfile: {
                      oncomplete : 'close',
                      multiple : true
                    }
                  },
                 // Insert in CKEditor when choosing files
                  getFileCallback : (files, fm) => {
                    let imgs = [];
                            fm.getUI('cwd').trigger('unselectall');
                            $.each(files, function(i, f) {
                              if (f && f.mime.match(/^image\//i)) {
                                imgs.push(fm.convAbsUrl(f.url));
                            } else {
                              editor.execute('link', fm.convAbsUrl(f.url));
                            }
                          });
                          if (imgs.length) {
                            insertImages(imgs);
                          }
                        }
                    }).elfinder('instance');
                    done();
                  }
                 });
                };
      
                  // elFinder instance
                  let _fm;
      
                  if (ckf) {
                    // Take over ckfinder execute()
                    ckf.execute = () => {
                      getfm().then(fm => {
                        fm.getUI().dialogelfinder('open');
                      });
                      };
                  }
      
                  // Make uploader
                  const uploder = function(loader) {
                    let upload = function(file, resolve, reject) {
                      getfm(uploadTargetHash{$ckeditor_id}).then(fm => {
                        let fmNode = fm.getUI();
                              fmNode.dialogelfinder('open');
                              fm.exec('upload', {files: [file], target: uploadTargetHash{$ckeditor_id}}, void(0), uploadTargetHash{$ckeditor_id})
                                  .done(data => {
                          if (data.added && data.added.length) {
                            fm.url(data.added[0].hash, { async: true }).done(function(url) {
                              resolve({
                                 'default': fm.convAbsUrl(url)
                              });
                              fmNode.dialogelfinder('close');
                            }).fail(function() {
                            reject('errFileNotFound');
                          });
                        } else {
                            reject(fm.i18n(data.error? data.error : 'errUpload'));
                            fmNode.dialogelfinder('close');
                          }
                        })
                        .fail(err => {
                          const error = fm.parseError(err);
                          reject(fm.i18n(error? (error === 'userabort'? 'errAbort' : error) : 'errUploadNoFiles'));
                        });
                          }).catch((fm, err) => {
                        const error = fm.parseError(err);
                        reject(fm.i18n(error? (error === 'userabort'? 'errAbort' : error) : 'errUploadNoFiles'));
                      });
                      };
      
                      this.upload = function() {
                        return new Promise(function(resolve, reject) {
                          if (loader.file instanceof Promise || (loader.file && typeof loader.file.then === 'function')) {
                            loader.file.then(function(file) {
                              upload(file, resolve, reject);
                            });
                          } else {
                            upload(loader.file, resolve, reject);
                          }
                          });
                      };
                      this.abort = function() {
                        _fm && _fm.getUI().trigger('uploadabort');
                      };
                  };
      
                  // Set up image uploader
                  fileRepo.createUploadAdapter = loader => {
                  return new uploder(loader);
               };
             })

              .catch( error => {
                  console.error( error );
              } );
              

          </script>
           ";

    return $field;
  }

/**
* Create form textarea field with ckeditor for image icon and source only
* @param string $name
* @param string|null $value
* @param int|null $width
* @param int|null $height
* @return string
 */
  public static function fileFieldImageCkEditor(string $name, string|null $value = null, int|null $width = null, int|null $height = null): string
  {
    $ckeditor_id = str_replace('[', '', $name);
    $ckeditor_id = str_replace(']', '', $ckeditor_id);
    $connector = static::getElFinderConnector();
    $language_code = static::getWysiwygLanguage();

    $field = '<textarea name="' . $name . '"  id="' . $ckeditor_id . '" /></textarea>';

    $field .= "<script>
              // elfinder folder hash of the destination folder to be uploaded in this CKeditor 5
              const uploadTargetHash{$ckeditor_id} = 'l2_Q0stRmlsZXM_{$ckeditor_id}';
              // elFinder connector URL
              //const connectorUrl = 'php/connector.minimal.php';
              const connectorUrl{$ckeditor_id} = '{$connector}';
              
              ClassicEditor
              .create( document.querySelector( '#{$ckeditor_id}' ), {
                language: '{$language_code}',                
                 toolbar: {
                    items: [
                       'ckfinder',
                    ],
                      shouldNotGroupWhenFull: false
                  },
              } )

//elfinder
             .then(editor => {
                const ckf = editor.commands.get('ckfinder'),
                fileRepo = editor.plugins.get('FileRepository'),
                ntf = editor.plugins.get('Notification'),
                i18 = editor.locale.t,
                  // Insert images to editor window
                insertImages = urls => {
                  const imgCmd = editor.commands.get('imageUpload');
                  if (!imgCmd.isEnabled) {
                    ntf.showWarning(i18('Could not insert image at the current position.'), {
                                title: i18('Inserting image failed'),
                                namespace: 'ckfinder'
                            });
                      return;
                      }
                        editor.execute('imageInsert', { source: urls });
                    },
                    // To get elFinder instance
                    getfm = open => {
                  return new Promise((resolve, reject) => {
                    // Execute when the elFinder instance is created
                    const done = () => {
                      if (open) {
                        // request to open folder specify
                        if (!Object.keys(_fm.files()).length) {
                          // when initial request
                          _fm.one('open', () => {
                            _fm.file(open)? resolve(_fm) : reject(_fm, 'errFolderNotFound');
                          });
                        } else {
                          // elFinder has already been initialized
                          new Promise((res, rej) => {
                            if (_fm.file(open)) {
                              res();
                            } else {
                              // To acquire target folder information
                              _fm.request({cmd: 'parents', target: open}).done(e =>{
                                _fm.file(open)? res() : rej();
                              }).fail(() => {
                                rej();
                              });
                            }
                          }).then(() => {
                            // Open folder after folder information is acquired
                            _fm.exec('open', open).done(() => {
                              resolve(_fm);
                            }).fail(err => {
                              reject(_fm, err? err : 'errFolderNotFound');
                            });
                                                }).catch((err) => {
                            reject(_fm, err? err : 'errFolderNotFound');
                          });
                          }
                      } else {
                        // show elFinder manager only
                        resolve(_fm);
                      }
                    };
            
                    // Check elFinder instance
                    if (_fm) {
                      // elFinder instance has already been created
                      done();
                    } else {
                      // To create elFinder instance
                      $.dialogelfinder = jQuery.dialogelfinder;
                      _fm = $('<div/>').dialogelfinder({
                            // dialog title
                            title : 'File Manager',
                            // connector URL
                            url : connectorUrl{$ckeditor_id},
                            // start folder setting
                            startPathHash : open? open : void(0),
                            // Set to do not use browser history to un-use location.hash
                            useBrowserHistory : false,
                            // Disable auto open
                            autoOpen : false,
                            // elFinder dialog width
                            width : '80%',
                            // set getfile command options
                            commandsOptions : {
                        getfile: {
                          oncomplete : 'close',
                          multiple : true
                        }
                      },
                     // Insert in CKEditor when choosing files
                      getFileCallback : (files, fm) => {
                        let imgs = [];
                        fm.getUI('cwd').trigger('unselectall');
                        $.each(files, function(i, f) {
                          if (f && f.mime.match(/^image\//i)) {
                            imgs.push(fm.convAbsUrl(f.url));
                        } else {
                          editor.execute('link', fm.convAbsUrl(f.url));
                        }
                        });
                        if (imgs.length) {
                          insertImages(imgs);
                        }
                      }
                      }).elfinder('instance');
                      done();
                    }
                   });
                  };
            
                    // elFinder instance
                    let _fm;
        
                    if (ckf) {
                      // Take over ckfinder execute()
                      ckf.execute = () => {
                        getfm().then(fm => {
                          fm.getUI().dialogelfinder('open');
                        });
                        };
                    }
        
                    // Make uploader
                    const uploder = function(loader) {
                      let upload = function(file, resolve, reject) {
                        getfm(uploadTargetHash{$ckeditor_id}).then(fm => {
                          let fmNode = fm.getUI();
                                fmNode.dialogelfinder('open');
                                fm.exec('upload', {files: [file], target: uploadTargetHash{$ckeditor_id}}, void(0), uploadTargetHash{$ckeditor_id})
                                    .done(data => {
                            if (data.added && data.added.length) {
                              fm.url(data.added[0].hash, { async: true }).done(function(url) {
                                resolve({
                                   'default': fm.convAbsUrl(url)
                                });
                                fmNode.dialogelfinder('close');
                              }).fail(function() {
                                reject('errFileNotFound');
                              });
                          } else {
                              reject(fm.i18n(data.error? data.error : 'errUpload'));
                              fmNode.dialogelfinder('close');
                            }
                          })
                          .fail(err => {
                            const error = fm.parseError(err);
                            reject(fm.i18n(error? (error === 'userabort'? 'errAbort' : error) : 'errUploadNoFiles'));
                          });
                            }).catch((fm, err) => {
                          const error = fm.parseError(err);
                          reject(fm.i18n(error? (error === 'userabort'? 'errAbort' : error) : 'errUploadNoFiles'));
                        });
                        };
        
                        this.upload = function() {
                          return new Promise(function(resolve, reject) {
                            if (loader.file instanceof Promise || (loader.file && typeof loader.file.then === 'function')) {
                              loader.file.then(function(file) {
                                upload(file, resolve, reject);
                              });
                            } else {
                              upload(loader.file, resolve, reject);
                            }
                            });
                        };
                        this.abort = function() {
                          _fm && _fm.getUI().trigger('uploadabort');
                        };
                    };
            
                    // Set up image uploader
                    fileRepo.createUploadAdapter = loader => {
                      return new uploder(loader);
                    };
                  })

            .catch( error => {
              console.error( error );
            } );
          </script>";

    return $field;
  }

  /**
   * Clean html code image
   *
   * @param string $image
   * @return string $image, without html
   *
   */
  public static function getWysiwygImageAlone(string $image): string
  {
    $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

    if (!empty($image)) {
      $doc = new DOMDocument();
      libxml_use_internal_errors(true);

      $doc->loadHTML($image);
      $xpath = new DOMXPath($doc);

      $image = $xpath->evaluate("string(//img/@src)");
      //$image = CLICSHOPPING::getConfig('http_server', 'Shop') . $image;

      $image = htmlspecialchars($image, ENT_QUOTES | ENT_HTML5);
      $image = strstr($image, $CLICSHOPPING_Template->getDirectoryShopTemplateImages());
      $image = str_replace($CLICSHOPPING_Template->getDirectoryShopTemplateImages(), '', $image);
      $image_end = strstr($image, '&quot;');
      $image = str_replace($image_end, '', $image);
      $image = str_replace($CLICSHOPPING_Template->getDirectoryShopSources(), '', $image);

      libxml_clear_errors();
    }

    return $image;
  }
}