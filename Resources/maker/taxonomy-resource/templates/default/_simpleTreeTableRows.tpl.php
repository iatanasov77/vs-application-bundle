<tr data-node-id="{{ item.id }}" {% if parentId %}data-node-pid="{{ item.parent.id }}"{% endif %}>
	<td>{{ loop.index }}</td>
	
<?php foreach ( $entity_fields as $field ): ?>
    <td>{{ <?= $helper->getEntityFieldPrintCode( 'item', $field ) ?> }}</td>
<?php endforeach; ?>

    <td>
        {% for locale in translations[item.id] %}
            <i class="flag flag-{{ locale | split( '_' )[1] | lower }}"></i>
            {% if not loop.last %}&nbsp;{% endif %}
        {% endfor %}
    </td>
    <td>
    	<div class="btn-group">
            <a class="btn btn-primary" href="{{ path('<?= $route_name ?>_update', {'id': item.id}) }}">
                <i class="fas fa-edit"></i>
            </a>
            <a class="btn btn-danger btnDelete" 
                href="{{ path('<?= $route_name ?>_delete', {'id': item.id}) }}"
                data-csrfToken="{{ csrf_token( item.id ) }}"
                title="Delete"
            >
                <i class="icon_close_alt2"></i>
            </a>
        </div>
    </td>
</tr>

{% for child in item.children %}
	{% include '<?= $templates_path ?>/_simpleTreeTableRows.html.twig' with {'parentId': item.id, 'taxon': child} %}   
{% endfor %}
