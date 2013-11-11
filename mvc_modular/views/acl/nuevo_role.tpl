<h2>Agregar Nuevo role</h2>

<form name="form1" id="form1" method="post" action="{$_layoutParams.root}acl/nuevo_role">
    <input type="hidden" name="guardar" value="1">
    <p>
    Nombre del role:
    <input type="text" name="role" id="role" value="" />
    </p>
    <input type="submit" value="Guardar" />
</form>