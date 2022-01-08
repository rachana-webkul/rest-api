<?php

namespace Webkul\RestApi\Http\Controllers\V1\Admin\Setting;

use Illuminate\Support\Facades\Event;
use Webkul\Inventory\Http\Requests\InventorySourceRequest;
use Webkul\Inventory\Repositories\InventorySourceRepository;
use Webkul\RestApi\Http\Resources\V1\Shop\Inventory\InventorySourceResource;

class InventorySourceController extends SettingController
{
    /**
     * Repository class name.
     *
     * @return string
     */
    public function repository()
    {
        return InventorySourceRepository::class;
    }

    /**
     * Resource class name.
     *
     * @return string
     */
    public function resource()
    {
        return InventorySourceResource::class;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(InventorySourceRequest $inventorySourceRequest)
    {
        $data = $inventorySourceRequest->all();

        $data['status'] = ! isset($data['status']) ? 0 : 1;

        Event::dispatch('inventory.inventory_source.create.before');

        $inventorySource = $this->getRepositoryInstance()->create($data);

        Event::dispatch('inventory.inventory_source.create.after', $inventorySource);

        return response([
            'data'    => new InventorySourceResource($inventorySource),
            'message' => __('rest-api::app.common-response.success.create', ['name' => 'Inventory source']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InventorySourceRequest $inventorySourceRequest, $id)
    {
        $data = $inventorySourceRequest->all();

        $data['status'] = ! isset($data['status']) ? 0 : 1;

        Event::dispatch('inventory.inventory_source.update.before', $id);

        $inventorySource = $this->getRepositoryInstance()->update($data, $id);

        Event::dispatch('inventory.inventory_source.update.after', $inventorySource);

        return response([
            'data'    => new InventorySourceResource($inventorySource),
            'message' => __('rest-api::app.common-response.success.update', ['name' => 'Inventory source']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->getRepositoryInstance()->findOrFail($id);

        if ($this->getRepositoryInstance()->count() == 1) {
            return response([
                'message' => __('rest-api::app.common-response.error.last-item-delete', ['name' => 'inventory source']),
            ], 400);
        }

        Event::dispatch('inventory.inventory_source.delete.before', $id);

        $this->getRepositoryInstance()->delete($id);

        Event::dispatch('inventory.inventory_source.delete.after', $id);

        return response([
            'message' => __('rest-api::app.common-response.success.delete', ['name' => 'Inventory source']),
        ]);
    }
}
