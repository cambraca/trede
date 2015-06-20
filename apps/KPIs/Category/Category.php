<?php

namespace KPIs;

include_once 'packages/Entity/Entity/Entity.api.inc';

//class Category extends \jQWidgets\Grid {
class Category extends \Entity\Entity {
  use \Entity\Entity\SimpleDBMapper, \Entity\Entity\Fieldable, \Entity\Entity\Page;

  protected static $initial_state = 'on';

  protected function table() {
    return 'kpi_category';
  }

  protected function properties() {
    return [
      'id' => [
        'type' => 'id',
      ],
      'items' => [
        'type' => 'collection',
        'entity' => 'KPIs\\Item',
      ],
    ];
  }

  protected function fields() {
    return [
      'name' => [
        'type' => 'Field\\Text',
        'label' => 'Name',
        'required' => TRUE,
        'storage' => 'internal', //in the same table
      ],
      'description' => [ //data stored in a separate table
        'type' => 'Field\\LongText',
        'label' => 'Description',
      ],
    ];
  }

}
