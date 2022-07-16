<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\ClicShoppingAdmin;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Language;

  class HTMLOverrideAdmin extends HTML
  {
    /*
     *  remplace les espaces par un +
     *
     * @param string $string
     * @return string $string,
     *
     */

    public static function sanitizeReplace(string $string): string
    {
      $string = preg_replace("/ /", "+", $string);
      return preg_replace("/[<>]/", '_', $string);
    }

    /*
     *  Ckeditor cdn version
     *
     * @param string $string
     * @return string $string,
     */
    public static function getCkeditor(): string
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $code = $CLICSHOPPING_Language->getCode();
      $version ='34.2.0';
      $type = 'super-build';

      $script = '<script src="https://cdn.ckeditor.com/ckeditor5/' . $version . '/' . $type . '/ckeditor.js"></script>';
      $script .= '<script src="https://cdn.ckeditor.com/ckeditor5/' . $version . '/' . $type . '/translations/' . $code . '.js"></script>';

      return $script;
    }

    /**
     * @return string
     */
    public static function getCkeditorLanguage() :string
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $code = $CLICSHOPPING_Language->getCode();

      return $code;
    }


    /**
     * @return string
     */
    private static function getElFinderConnector() :string
    {
      $connector =  '../ext/elFinder-master/php/connector.minimal.php';

      return $connector;
    }

    /**
     * @param string $name
     * @return string
     */
    public static function CkEditorId(string $name) :string
    {
      $result = str_replace('[', '', $name);
      $result = str_replace(']', '', $result);

      return $result;
    }


    /*
     * Outputs a form textarea field with ckeditor
     *
     * @param string $name The name and ID of the textarea field
     * @param string $value The default value for the textarea field
     * @param int $width The width of the textarea field
     * @param int $height The height of the textarea field
     * @param string $parameters Additional parameters for the textarea field
     * @param boolean $override Override the default value with the value found in the GET or POST scope
     *
     */
    public static function textAreaCkeditor(string $name, ?string $value = null, int $width = 750, int $height = 200, ?string $text = null, ?string $parameters = null, bool $override = true): string
    {
      $field = '<textarea name="' . HTML::output($name) . '"';

      if (!\is_null($parameters)) $field .= ' ' . $parameters;
      $field .= ' />';
      
      if (($override === true) && ((isset($_GET[$name]) && \is_string($_GET[$name])) || (isset($_POST[$name]) && \is_string($_POST[$name])))) {
        if (isset($_GET[$name]) && \is_string($_GET[$name])) {
          $field .= HTML::outputProtected($_GET[$name]);
        } elseif (isset($_POST[$name]) && \is_string($_POST[$name])) {
          $field .= HTML::outputProtected($_POST[$name]);
        }
      } elseif (!\is_null($text)) {
        $field .= HTML::outputProtected($text);
      }

      $field .= '</textarea>';

      $ckeditor_id = str_replace('[', '', $name);
      $ckeditor_id = str_replace(']', '', $ckeditor_id);
      $connector = static::getElFinderConnector();
      $language_code = static::getCkeditorLanguage();


      $field .= "<script>
// elfinder folder hash of the destination folder to be uploaded in this CKeditor 5
const uploadTargetHash{$ckeditor_id} = 'l2_Q0stRmlsZXM' . $ckeditor_id;

// elFinder connector URL
//const connectorUrl = 'php/connector.minimal.php';
const connectorUrl{$ckeditor_id} = '{$connector}';
 
CKEDITOR.ClassicEditor
   .create(document.getElementById('{$ckeditor_id}'), {
/*
ClassicEditor
    .create(document.getElementById('{$ckeditor_id}') , {
*/    
        language: '{$language_code}',
//        toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'ckfinder', '|' , 'link', 'mediaEmbed',  '|' ,'undo', 'redo']

               toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                        'bulletedList', 'numberedList', 'todoList', '|',
                        'outdent', 'indent', '|',
                        'undo', 'redo',
                        '-',
                        'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                        'alignment', '|',
                        'findAndReplace', 'selectAll', '|',
                        'link', 'ckfinder', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                        'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                        'exportPDF','exportWord', '|',                       
                        'sourceEditing'
                    ],
                    shouldNotGroupWhenFull: true
                },
                
                list: {
                    properties: {
                        styles: true,
                        startIndex: true,
                        reversed: true
                    }
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
                
                fontFamily: {
                    options: [
                        'default',
                        'Arial, Helvetica, sans-serif',
                        'Courier New, Courier, monospace',
                        'Georgia, serif',
                        'Lucida Sans Unicode, Lucida Grande, sans-serif',
                        'Tahoma, Geneva, sans-serif',
                        'Times New Roman, Times, serif',
                        'Trebuchet MS, Helvetica, sans-serif',
                        'Verdana, Geneva, sans-serif'
                    ],
                    supportAllValues: true
                },
                
                fontSize: {
                    options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                    supportAllValues: true
                },
                
                 link: {
                    decorators: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        toggleDownloadable: {
                            mode: 'manual',
                            label: 'Downloadable',
                            attributes: {
                                download: 'file'
                            }
                        }
                    }
                },
                
                removePlugins: [
                    // These two are commercial, but you can try them out without registering to a trial.
                    // 'ExportPdf',
                    // 'ExportWord',
                    'CKBox',
                    //'CKFinder',
                    'EasyImage',
                    // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                    // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                    // Storing images as Base64 is usually a very bad idea.
                    // Replace it on production website with other solutions:
                    // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                    // 'Base64UploadAdapter',
                    'RealTimeCollaborativeComments',
                    'RealTimeCollaborativeTrackChanges',
                    'RealTimeCollaborativeRevisionHistory',
                    'PresenceList',
                    'Comments',
                    'TrackChanges',
                    'TrackChangesData',
                    'RevisionHistory',
                    'Pagination',
                    'WProofreader',
                    // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                    // from a local file system (file://) - load this site via HTTP server if you enable MathType
                    'MathType'
                ]

    } )
    
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
   .catch(error => {
        console.error( error );
   });
            </script>";

      return $field;
    }

    /*
     * Create form textarea field with ckeditor for image icon and source only
     *
     * @param string $name The name and ID of the textarea field
     *
     */

    public static function fileFieldImageCkEditor(string $name, ?string $value = null, ?int $width = null, ?int $height = null): string
    {
      if (\is_null($height)) {
        $height = '250';
      }

      if (\is_null($width)) {
        $width = '250';
      }

      $field = '<textarea name="' . HTML::output($name) . '" id="' . HTML::output($name) . '" /></textarea>';

      $connector = static::getElFinderConnector();

      $field .= "<script>
// elfinder folder hash of the destination folder to be uploaded in this CKeditor 5
const uploadTargetHashImage = 'l2_Q0stRmlsZXM_' . $name;

// elFinder connector URL
//const connectorUrl = 'php/connector.minimal.php';
const connectorUrlImage = '{$connector}';

// To create CKEditor 5 classic editor
CKEDITOR.ClassicEditor
   .create(document.getElementById('{$name}'), {
        toolbar: {
                items: [
                    'ckfinder', '|',
                    'sourceEditing'
                    ],
                 shouldNotGroupWhenFull: true
        },
                    
        removePlugins: [
            // These two are commercial, but you can try them out without registering to a trial.
            // 'ExportPdf',
            // 'ExportWord',
            'CKBox',
            //'CKFinder',
            'EasyImage',
            // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
            // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
            // Storing images as Base64 is usually a very bad idea.
            // Replace it on production website with other solutions:
            // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
            // 'Base64UploadAdapter',
            'RealTimeCollaborativeComments',
            'RealTimeCollaborativeTrackChanges',
            'RealTimeCollaborativeRevisionHistory',
            'PresenceList',
            'Comments',
            'TrackChanges',
            'TrackChangesData',
            'RevisionHistory',
            'Pagination',
            'WProofreader',
            // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
            // from a local file system (file://) - load this site via HTTP server if you enable MathType
            'MathType'
        ]
    } )
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
          _fm = $('<div/>').dialogelfinder({
            // dialog title
            title : 'File Manager',
            // connector URL
            url : connectorUrlImage,
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
        getfm(uploadTargetHashImage).then(fm => {
          let fmNode = fm.getUI();
              fmNode.dialogelfinder('open');
              fm.exec('upload', {files: [file], target: uploadTargetHashImage}, void(0), uploadTargetHashImage)
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
    .catch(error => {
    console.error( error );
  });
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
    public static function getCkeditorImageAlone(string $image): string
    {
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (!empty($image)) {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);

        $doc->loadHTML($image);
        $xpath = new \DOMXPath($doc);

        $image = $xpath->evaluate("string(//img/@src)");
        $image = CLICSHOPPING::getConfig('http_server', 'Shop') . $image;

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

    /**
     * Pulldown products
     *
     * @param string $name , $parameters, $exclude
     * @return string $select_string, the pulldown value of products
     *
     */
    public static function selectMenuProductsPullDown(string $name, $parameters = '', $exclude = '', string $class = 'form-control'): string
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_Currencies = Registry::get('Currencies');

      if (empty($exclude)) {
        $exclude = [];
      }

      $select_string = '<select name="' . $name . '"';

      if ($parameters) {
        $select_string .= ' ' . $parameters;
      }

      if (!empty($class)) $select_string .= ' class="' . $class . '"';

      $select_string .= ' />';

      $all_groups = [];

      $QcustomersGroups = $CLICSHOPPING_Db->prepare('select customers_group_name,
                                                             customers_group_id
                                                      from :table_customers_groups
                                                      order by customers_group_id
                                                    ');
      $QcustomersGroups->execute();

      while ($existing_groups = $QcustomersGroups->fetch()) {
        $all_groups[$existing_groups['customers_group_id']] = $existing_groups['customers_group_name'];
      }

      $Qproducts = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                     pd.products_name,
                                                     p.products_price
                                              from :table_products p,
                                                   :table_products_description pd
                                              where p.products_id = pd.products_id
                                              and pd.language_id = :language_id
                                              and p.products_archive = 0
                                              order by products_name
                                             ');
      $Qproducts->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
      $Qproducts->execute();

      while ($Qproducts->fetch()) {
        if (MODE_B2B_B2C == 'true') {
          if (!\in_array($Qproducts->valueInt('products_id'), $exclude)) {

            $Qprice = $CLICSHOPPING_Db->prepare('select customers_group_price,
                                                  customers_group_id
                                          from :table_products_groups
                                          where products_id = :products_id
                                         ');
            $Qprice->bindInt(':products_id', $Qproducts->valueInt('products_id'));
            $Qprice->execute();

            $product_prices = [];

            while ($prices_array = $Qprice->fetch()) {
              $product_prices[$prices_array['customers_group_id']] = $prices_array['customers_group_price'];
            }

            $price_string = '';
            $sde = 0;
//while(list($sdek,$sdev)=each($all_groups)){
            foreach ($all_groups as $sdek => $sdev) {
              if (!\in_array($Qproducts->valueInt('products_id') . ":" . (int)$sdek, $exclude)) {
                if ($sde)
                  $price_string .= ' - ';
                $price_string .= $sdev . ' : ' . $CLICSHOPPING_Currencies->format(isset($product_prices[$sdek]) ? $product_prices[$sdek] : $Qproducts->valueDecimal('products_price'));
                $sde = 1;
              }
            }

// Ajouter VISITOR_NAME . ': ' . $CLICSHOPPING_Currencies->format($Qproducts->valueDecimal('products_price')) pour permettre d'afficher le prix des clients qui ne font pas partie d'un groupe B2B(
            $select_string .= '<option value="' . $Qproducts->valueInt('products_id') . '">' . HTML::outputProtected($Qproducts->value('products_name')) . ' (' . CLICSHOPPING::getDef('visitor_name') . ': ' . $CLICSHOPPING_Currencies->format($Qproducts->valueDecimal('products_price')) . ' - ' . $price_string . ')</option>';
          }
        } else {
          if (!\in_array($Qproducts->valueInt('products_id'), $exclude)) {
            $select_string .= '<option value="' . $Qproducts->valueInt('products_id') . '">' . HTML::outputProtected($Qproducts->value('products_name')) . ' (' . $CLICSHOPPING_Currencies->format($Qproducts->valueDecimal('products_price')) . ')</option>';
          }
        }

// ####### END  #######
      }

      $select_string .= '</select>';

      return $select_string;
    }


    /**
     * javascript to dynamically update the states/provinces list when the country is changed
     * TABLES: zones
     */
    public static function getJsZoneList(string $country, string $form, string $field): string
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcountries = $CLICSHOPPING_Db->prepare('select distinct zone_country_id
                                               from :table_zones
                                               where  zone_status = 0
                                               order by zone_country_id
                                              ');
      $Qcountries->execute();

      $num_country = 1;
      $output_string = '';

      while ($countries = $Qcountries->fetch()) {
        if ($num_country == 1) {
          $output_string .= '  if (' . $country . ' == "' . (int)$countries['zone_country_id'] . '") {' . "\n";
        } else {
          $output_string .= '  } else if (' . $country . ' == "' . (int)$countries['zone_country_id'] . '") {' . "\n";
        }

        $Qzone = $CLICSHOPPING_Db->prepare('select zone_name,
                                                   zone_id
                                            from :table_zones
                                            where  zone_country_id = :zone_country_id
                                            and zone_status = 0
                                            order by zone_name
                                          ');
        $Qzone->bindInt(':zone_country_id', $countries['zone_country_id']);

        $Qzone->execute();

        $num_state = 1;

        while ($states = $Qzone->fetch()) {
          if ($num_state == '1') $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . CLICSHOPPING::getDef('text_selected') . '", "");' . "\n";
          $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $states['zone_name'] . '", "' . $states['zone_id'] . '");' . "\n";
          $num_state++;
        }

        $num_country++;
      }

      $output_string .= '  } else {' . "\n" .
        '    ' . $form . '.' . $field . '.options[0] = new Option("' . CLICSHOPPING::getDef('text_select') . '", "");' . "\n" .
        '  }' . "\n";

      return $output_string;
    }

    /**
     * @param array $data
     * @param string $filename
     * @param string $delimiter
     * @param string $extension
     * @param string $enclosure
     */
    public function exportDataToCsv(array $data, string $filename = 'export', string $delimiter = ';', string $extension='csv', string $enclosure = '"')
    {
      header("Content-disposition: attachment; filename=$filename.$extension");
      header("Content-Type: text/csv");

      $fp = fopen('php://output', 'w');

      // Insert the UTF-8 BOM in the file
      fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

      // I add the array keys as CSV headers
      fputcsv($fp,array_keys($data[0]), $delimiter, $enclosure);

      // Add all the data in the file
      foreach ($data as $fields) {
        fputcsv($fp, $fields, $delimiter, $enclosure);
      }

      fclose($fp);

      die();
    }
  }