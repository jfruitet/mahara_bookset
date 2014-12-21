{include file="header.tpl"}
<div id="booksetwrap">
<br /><br /><br />

    <div class="rbuttons">
        <a class="btn" href="{$WWWROOT}artefact/bookset/new.php?id={$bookset}">{str section="artefact.bookset" tag="newcomponent"}</a>
    </div>
    {if $tags}<p class="tags s"><label>{str tag=tags}:</label> {list_tags owner=$owner tags=$tags}</p>{/if}
{if !$components.data}
    <div>{$booksetcomponentsdescription}</div>
    <div class="message">{$strnocomponentsaddone|safe}</div>
{else}
<table id="componentslist" class="fullwidth listing">
<tr><td width="90%">
<h3 align="center"><a href="{$WWWROOT}artefact/bookset/index.php?id={$bookset}">{$artefacttitle|safe}</a></h3>
{if $artefactdescription}
<p>{$artefactdescription|safe}
{if $artefactmotivation}
<br />{$artefactmotivation|safe}
{/if}
</p>
{/if}
</td><td>
              <a href="{$WWWROOT}artefact/bookset/valide/index.php?id={$bookset}" title="{str tag=validate section=artefact.bookset}" >
                    <img src="{$iconcheckpath}" alt="{str(tag=validatespecific section=artefact.bookset arg1=$artefacttitle)|escape:html|safe}"></a>
</td></tr></table>
<table id="componentslist" class="fullwidth listing">
    <thead>
        <tr>
            <th width="10%">{str tag='code' section='artefact.bookset'}</th>
            <th width="70%">{str tag='title' section='artefact.bookset'}
			<th width="10%">{str tag='scale' section='artefact.bookset'}</th>
<!--			<th>{str tag='valueindex' section='artefact.bookset'}</th> -->
<!--			<th>{str tag='optioncomponent' section='artefact.bookset'}</th> -->
<th with="10%">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
		{foreach from=$components.data item=componentr}
			<tr class="{cycle values='r0,r1'}">
				<td>{$componentr->code|safe}</td>
				{if $componentr->optioncomponent == 0}
						<td class="normal">{$componentr->title|safe}</td>
				{else}
						{if $componentr->optioncomponent == 1}
							<td class="optionnal">{$componentr->title|safe}</td>				
						{else}
							<td class="header">{$componentr->title|safe}</td>
						{/if}
				{/if}
				
				<td>{$componentr->scale|clean_html|safe}</td>
<!--				<td>{$componentr->valueindex|safe}</td>  -->
<!--				<td>{$componentr->optioncomponent|safe}</td>  -->
				<td class="buttonscell btns2 planscontrols"> 
 				
                <a href="{$WWWROOT}artefact/bookset/edit/component.php?id={$componentr->component}" title="{str tag=edit}">
                    <img src="{theme_url filename='images/btn_edit.png'}" alt="{str(tag=editspecific arg1=$componentr->title)|escape:html|safe}">
                </a>
                <a href="{$WWWROOT}artefact/bookset/delete/component.php?id={$componentr->component}" title="{str tag=delete}">
                    <img src="{theme_url filename='images/btn_deleteremove.png'}" alt="{str(tag=deletespecific arg1=$componentr->title)|escape:html|safe}">
                </a>
                <a href="{$WWWROOT}artefact/bookset/bookset.php?id={$componentr->parent}&amp;componentid={$componentr->component}&amp;direction=1&amp;order={$strorder}&amp;offset=0&amp;limit=100" title="{str tag=movecomponentdown}">
                    <img src="{theme_url filename='images/btn_movedown.png'}" alt="{str(tag=editspecific arg1=$componentr->title)|escape:html|safe}">
                </a>
                <a href="{$WWWROOT}artefact/bookset/bookset.php?id={$componentr->parent}&amp;componentid={$componentr->component}&amp;direction=0&amp;order={$strorder}&amp;offset=0&amp;limit=100" title="{str tag=movecomponentup}">
                    <img src="{theme_url filename='images/btn_moveup.png'}" alt="{str(tag=editspecific arg1=$componentr->title)|escape:html|safe}">
                </a>		
                <a href="{$WWWROOT}artefact/bookset/new.php?id={$componentr->parent}&amp;positionafter={$componentr->displayorder}" title="{str tag=insertcomponentafter section=artefact.bookset}">
                    <img src="{theme_url filename='images/btn_add.png'}" alt="{str(tag=editspecific arg1=$componentr->title)|escape:html|safe}">
                </a>

				</td>				

			</tr>
			<tr>
				<td class="normal" colspan="4"><i>{$componentr->description|safe}</i></td>
			</tr>
		{/foreach}
    </tbody>
</table>
   {$components.pagination|safe}
   <div align="center">{$urlallcomponents|safe} &nbsp; &nbsp; {$orderlist|safe}</div>
{/if}
</div>
{include file="footer.tpl"}
