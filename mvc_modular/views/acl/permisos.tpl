<h2>Administración de Permisos</h2>
{if isset($permisos) && count($permisos)}
    <table>
        <tr>
            <th>ID</th>
            <th>Permiso</th>
            <th>Key</th>
            <th></th>
        </tr>
         {foreach item=p from=$permisos}
         <tr>
             <td>{$p.id_permiso}</td>
             <td>{$p.permiso}</td>
             <td>{$p.key}</td>
             <td><a href="{$_layoutParams.root}acl/editar_permiso/{$p.id_permiso}">Editar</a></td>
         </tr>
         {/foreach}
    </table>
{/if}
<p><a href="{$_layoutParams.root}acl/nuevo_permiso">Agregar Permiso</a></p>