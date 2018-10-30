<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *
 *
 */


  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_DefineLanguage = Registry::get('DefineLanguage');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_MessageStack = Registry::get('MessageStack');

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = HTML::sanitize($_GET['search']);
  }

  $languages = $CLICSHOPPING_Language->getLanguages();

  if ($CLICSHOPPING_MessageStack->exists('content_group')) {
    echo $CLICSHOPPING_MessageStack->get('content_group');
  }
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . '/categories/define_language.gif', $CLICSHOPPING_DefineLanguage->getDef('heading_title_2'), '40', '40'); ?></span>
          <span class="col-md-6 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_DefineLanguage->getDef('heading_title'); ?></span>
          <span class="col-md-5 text-md-right">
<?php
    echo HTML::form('define_language',$CLICSHOPPING_DefineLanguage->link('DefineLanguage&Save&ContentGroup=' . $_GET['ContentGroup'], 'post', 'enctype="multipart/form-data"'));
    echo '&nbsp;';
    echo HTML::button($CLICSHOPPING_DefineLanguage->getDef('button_back'), null, $CLICSHOPPING_DefineLanguage->link('DefineLanguage'), 'primary') . ' ' . HTML::button($CLICSHOPPING_DefineLanguage->getDef('button_save'), null, null, 'success') . ' ';
?>
           </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <ul class="nav nav-tabs flex-column flex-sm-row" role="tablist" id="myTab">
<?php
        for ($i=0, $n=count($languages); $i<$n; $i++) {
          echo '<li class="nav-item " ' . ($i === 0 ? 'active"' : '') . '><a href="#tab'.$i.'" data-target="#section_general_content_' . $languages[$i]['directory'] . '" role="tab" data-toggle="tab" class="nav-link">' . $CLICSHOPPING_Language->getImage($languages[$i]['code']) . '&nbsp;' . $languages[$i]['name'] . '</a></li>';
        }

        echo '<li class="nav-item"><a data-target="#section_general_content_translation_tab" role="tab" data-toggle="tab" class="nav-link">' . HTML::button($CLICSHOPPING_DefineLanguage->getDef('section_heading_translations'), null, null, 'primary', null, 'sm')  . '</a></li>';
?>
  </ul>

  <div class="tabsClicShopping">
    <div class="tab-content">
<?php
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
      <div clas="row adminformTitle" id="section_general_content_<?php echo $languages[$i]['directory']; ?>" class="tab-pane <?php echo ($i === 0 ? 'active' : ''); ?>">
        <table class="table table-hover">
          <thead>
          <tr class="dataTableHeadingRow">
            <th width="50%"><?php echo $CLICSHOPPING_DefineLanguage->getDef('table_heading_definition_key'); ?></th>
            <th><?php echo $CLICSHOPPING_DefineLanguage->getDef('table_heading_definition_value'); ?></th>
          </tr>
          </thead>
          <tbody>
<?php
    if (isset($search)) {
      $Qdefinitions = $CLICSHOPPING_DefineLanguage->db->prepare("select definition_key,
                                                                         definition_value
                                                                  from :table_languages_definitions
                                                                  where content_group = :content_group
                                                                  and (definition_key like " . "'%". $search . "%'" . " or definition_value like " . "'%". $search . "%'" . ")
                                                                  and languages_id = :languages_id
                                                                  order by definition_key
                                                                ");
      } else {

      $Qdefinitions = $CLICSHOPPING_DefineLanguage->db->prepare('select  id,
                                                                        definition_key,
                                                                        definition_value
                                                                  from :table_languages_definitions
                                                                  where content_group = :content_group
                                                                  and languages_id = :languages_id
                                                                  order by definition_key
                                                                ');
    }

    $Qdefinitions->bindValue(':content_group', $_GET['ContentGroup']);
    $Qdefinitions->bindInt(':languages_id', $languages[$i]['id']);
    $Qdefinitions->execute();

    while ($Qdefinitions->fetch()) {
?>
              <tr>
                <td style="word-break: break-all;"><?php echo $Qdefinitions->value('definition_key'); ?></td>
                <td><?php echo htmlentities($Qdefinitions->value('definition_value')); ?></td>
              </tr>
<?php
    }
?>
              </tbody>
            </table>
          </div>
<?php
  }
?>
<script>
  var  definition_key = "";

  $('#rowDelConfirm').on('hidden.bs.modal', function (e) {
  });

  function NewDef(defVar, place) {
    $(place).replaceWith('<textarea class="form-control" name="' + defVar + '"></textarea>');
  }

  function DeleteDef(defVar) {
    definition_key = defVar;
    $('#modalDefinitionKey').html(definition_key);
    $('#rowDelConfirm').modal('show');
  }

  $(function() {
    $('#rowDelConfirmButtonDelete').on('click', function() {
      $('#rowDelConfirm').modal('hide');
      $("." + definition_key).remove();
      $('form[name="define_language"]').append('<input type="hidden" name="delete[' + definition_key + ']" value="">');
    });
  });
</script>
<style>
  .table-hover > tbody > tr.new_definition_row:hover > td,
  .new_definition_row > td {
    background-color: #bddef9;
  }
</style>
  <div id="section_general_content_translation_tab" class="tab-pane">
    <table class="table table-hover">
      <thead>
      <tr class="dataTableHeadingRow">
        <th width="50%"><?php echo $CLICSHOPPING_DefineLanguage->getDef('table_heading_definition_key'); ?></th>
        <th><?php echo $CLICSHOPPING_DefineLanguage->getDef('table_heading_definition_value'); ?></th>
      </tr>
      </thead>

      <tbody>
<?php
    if (isset($search)) {
      $Qdefinitions = $CLICSHOPPING_DefineLanguage->db->prepare("select distinct definition_key
                                                                  from :table_languages_definitions
                                                                  where content_group = :content_group
                                                                  and (definition_key like " . "'%". $search . "%'" . " or definition_value like " . "'%". $search . "%'" . ")
                                                                  order by definition_key
                                                                 ");
    } else {
      $Qdefinitions = $CLICSHOPPING_DefineLanguage->db->prepare('select distinct definition_key
                                                                  from :table_languages_definitions
                                                                  where content_group = :content_group
                                                                  order by definition_key
                                                                 ');
    }

    $Qdefinitions->bindValue(':content_group', $_GET['ContentGroup']);
    $Qdefinitions->execute();

    while ($Qdefinitions->fetch()) {
?>
        <tr class="<?php echo $Qdefinitions->value('definition_key'); ?>">
          <td style="word-break: break-all;"><?php echo $Qdefinitions->value('definition_key'); ?><br /><span style="cursor:pointer" onclick="DeleteDef('<?php echo $Qdefinitions->value('definition_key'); ?>')"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_DefineLanguage->getDef('icon_delete')); ?></span></td>
          <td>
<?php
      for ($i=0, $n=count($languages); $i<$n; $i++) {
        $Tdefinitions = $CLICSHOPPING_DefineLanguage->db->prepare('select definition_key,
                                                                           definition_value
                                                                    from :table_languages_definitions
                                                                    where content_group = :content_group
                                                                    and languages_id = :languages_id
                                                                    and definition_key = :definition_key
                                                                    order by languages_id
                                                                  ');
        $Tdefinitions->bindValue(':content_group', $_GET['ContentGroup']);
        $Tdefinitions->bindValue(':definition_key', $Qdefinitions->value('definition_key'));
        $Tdefinitions->bindInt(':languages_id', $languages[$i]['id']);
        $Tdefinitions->execute();
?>
                      <br /><?php echo '<p class="text-info"><i><small>' . $languages[$i]['name'] . '</small></i></p>';
        if ($Tdefinitions->fetch() !== false) {
          do {
?>
                          <textarea class="form-control" name="definition_value[<?php echo $Tdefinitions->value('definition_key'); ?>][<?php echo $languages[$i]['id']; ?>]"><?php echo htmlentities($Tdefinitions->value('definition_value')); ?></textarea>
<?php
          } while ($Tdefinitions->fetch());
        } else {
?>
                        <span style="cursor:pointer" onclick="NewDef('definition_value[<?php echo $Qdefinitions->value('definition_key') . '][' . $languages[$i]['id'] . ']\', this)'; ?>"><i class="fas fa-plus" title="<?php echo $CLICSHOPPING_DefineLanguage->getDef('icon_add_new'); ?>"></i></span>
<?php
      }
    }
?>
          </td>
        </tr>
<?php
  }
?>
        <tr class="new_definition_row">
          <td>
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-addon"><i class="fas fa-plus-square" aria-hidden="true"></i></div>
                <?php echo HTML::inputField('new_definition_key', '', 'size="50" placeholder="' . $CLICSHOPPING_DefineLanguage->getDef('placeholder_new_definition_value') . '" maxlength="255" pattern="^[a-z0-9_]{1,255}$"'); ?>
              </div>
            </div>
            <?php echo $CLICSHOPPING_DefineLanguage->getDef('text_pattern'); ?> [a-z0-9_]
          </td>
          <td>
<?php
  for ($i=0, $n=count($languages); $i<$n; $i++) {
?>
            <br /><p class="text-info"><strong><i><small><?php echo $languages[$i]['name']; ?></small></i></strong></p>
            <textarea class="form-control" name="new_definition_value[<?php echo $languages[$i]['id']; ?>]"></textarea>
<?php
  }
?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    </form>

      <div class="modal fade" id="rowDelConfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content"> <!-- Modal Content -->
            <div class="modal-header"> <!-- Modal Header -->
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                <span class="sr-only"><?php echo $CLICSHOPPING_DefineLanguage->getDef('text_close'); ?></span>
              </button>
              <h4 class="modal-title"><?php echo $CLICSHOPPING_DefineLanguage->getDef('text_language_definition_delete_title'); ?></h4>
            </div>

            <div class="modal-body"> <!-- Modal Body -->
              <p><?php echo $CLICSHOPPING_DefineLanguage->getDef('text_language_definition_confirm_delete'); ?></p>
              <p id="modalDefinitionKey"></p>
            </div>

            <div class="modal-footer"> <!-- Modal Footer -->
              <button type="button" class="btn btn-danger" id="rowDelConfirmButtonDelete"><?php echo $CLICSHOPPING_DefineLanguage->getDef('button_delete'); ?></button>
              <button type="button" class="btn btn-link" data-dismiss="modal"><?php echo $CLICSHOPPING_DefineLanguage->getDef('button_cancel'); ?></button>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->
    </div>
  </div>
</div>