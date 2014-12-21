{foreach from=$components.data item=componentr}
	<tr class="{cycle values='r0,r1'}">
            <td>{$componentr->id|safe}</td>
            <td>{$componentr->title|safe}</td>
            <td>{$componentr->status|safe}</td>			
			<td>{$componentr->public|safe}</td>
			<td>{$componentr->owner|safe}</td> 
			<td>{$componentr->displayorder|safe}</td>			
	</tr>
{/foreach}
