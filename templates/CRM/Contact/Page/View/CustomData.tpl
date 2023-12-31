{*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
*}
{* template for custom data *}
{assign var="customDataGroupName" value=$customDataGroup.name}
{strip}
  {if $displayStyle neq 'tableOriented' and ($action eq 16 or $action eq 4)} {* Browse or View actions *}
    {assign var="customGroupDisplayDone" value=1}
    <div class="form-item">
      {include file="CRM/Custom/Page/CustomDataView.tpl"}
    </div>
  {/if}
{/strip}
{if $displayStyle eq 'tableOriented'}
  {include file='CRM/Profile/Page/MultipleRecordFieldsListing.tpl' showListing=1 dontShowTitle=1 pageViewType='customDataView'}
  {literal}
    <script type="text/javascript">
      CRM.$(function($) {
        var $table = $("#{/literal}custom-{$customGroupId}-table-wrapper{literal}");
        $('a.delete-custom-row', $table).on('click', function(e) {
          deleteRow($(this));
          e.preventDefault();
        });
        $(".crm-multifield-selector").on('click', '.delete-custom-row', function (e) {
          deleteRow($(this));
          e.preventDefault();
        });

        function deleteRow($el) {
          CRM.confirm({
            message: '{/literal}{ts escape='js'}Are you sure you want to delete this record?{/ts}{literal}'
          }).on('crmConfirm:yes', function() {
            var postUrl = {/literal}"{crmURL p='civicrm/ajax/customvalue' h=0}"{literal};
            var request = $.post(postUrl, $el.data('delete_params'));
            CRM.status({/literal}"{ts escape='js'}Record Deleted{/ts}"{literal}, request);
            request.done(function() {
              CRM.refreshParent($el);
            });
          })
        }
      });
    </script>
  {/literal}
{/if}

{* Todo: Comment on which part custom data gets displayed from the below code. *}
{foreach from=$viewCustomData item=customGroupWrapper}
  {foreach from=$customGroupWrapper item=customGroup key=customGroupId}
    {assign var="customRegion" value='contact-custom-data-'|cat:$customGroup.name}
    {crmRegion name=$customRegion}
      {if $customGroup.help_pre and !$customGroupDisplayDone}
        <div class="messages help">{$customGroup.help_pre}</div>
      {/if}
      {if $action eq 0 or $action eq 1 or $action eq 2 or $recordActivity}
        {include file="CRM/Contact/Form/CustomData.tpl" mainEdit=$mainEditForm}
      {/if}
      {if $mainEditForm}
        <script type="text/javascript">
          var showBlocks1 = new Array({$showBlocks1});
          var hideBlocks1 = new Array({$hideBlocks1});

          on_load_init_blocks(showBlocks1, hideBlocks1);
        </script>
      {else}
        <script type="text/javascript">
          var showBlocks = new Array({$showBlocks});
          var hideBlocks = new Array({$hideBlocks});

          {* hide and display the appropriate blocks as directed by the php code *}
          on_load_init_blocks(showBlocks, hideBlocks);
        </script>
      {/if}
      {if $customGroup.help_post and !$customGroupDisplayDone}
        <div class="messages help">{$customGroup.help_post}</div>
      {/if}
    {/crmRegion}
  {/foreach}
{/foreach}
