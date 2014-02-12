<?php

return array(

	'instance' => function()
    {
        return App::make('Application\Storage\Contracts\SyncInterface');
    },

    'create'   => function($system, $resource, $entity, $type)
    {
        $company_id = null;
        $representative_id = null;

        $class = explode('\\', get_class($this->resource));

        switch(end($class))
        {   
            case 'Erp':
                $erp_id = $resource->id;
            break;
            case 'Company':
                $company_id = $resource->id;
                $erp_id     = $resource->erp->id;
            break;
            case 'Representative':
                $representative_id = $resource->id;
                $company_id        = $resource->company->id;
                $erp_id            = $resource->company->erp->id;
            break;
        }

        return array(
            'erp_id'            => $erp_id,
            'company_id'        => $company_id,
            'representative_id' => $representative_id,
            'entity'            => $entity,
            'type'              => $type,
            'class'             => get_class($system),
            'status'            => 'processing',
        );
    },
);