<?php


namespace App\Services\Lists;


use App\Helpers\Core\Traits\Helpers;
use App\Models\Lists\Lists;
use App\Notifications\Lists\ListNotification;
use App\Services\AppService;
use App\Services\Subscriber\Helpers\BulkActionTrait;

class ListService extends AppService
{
    use Helpers, BulkActionTrait;
    public function __construct(Lists $lists)
    {
        $this->model = $lists;
    }


    public function save($options = [])
    {
        $this->model->fill(request()->all());

        if ($this->model->isDirty() && $this->model->id) {
            notify()
                ->on('list_updated')
                ->with($this->model)
                ->send(ListNotification::class);
        }

        $this->model->save();

        $subscribers = [];

        $this->model->segments()->sync([]);

        $this->model->subscribers()->sync([]);

        if (count(request('segments', [])))
            $this->syncSegments();

        if (count(request('subscribers', [])))
            $subscribers = $this->syncSubscribers();

        if (request('type') == 'imported') {
            $this->syncImportedSubscribers(
                $subscribers ?? []
            );
        }

        return $this->model;
    }

    public function syncSubscribers()
    {
        $ids = $this->checkMakeArray(
            request('subscribers')
        );
        $subscribers = $this->model->subscribers()
            ->sync( $ids )['attached'];

        return $subscribers ?? [];
    }

    public function syncImportedSubscribers($withExisting = [])
    {
        if (!$this->model->segments->count())
            return $this->model;

        $subscribers = $this->model
            ->subscriberBuilder()
            ->pluck('id')
            ->toArray();

        $subscribers = array_unique(array_merge($subscribers, $withExisting));

        $this->model->subscribers()
            ->sync($subscribers);

        return $this->model;
    }

    public function syncSegments()
    {
        $segments = $this->checkMakeArray(
            request('segments')
        );
        return $this->model->segments()->sync(
            $segments
        );
    }


    public function addBulkListSubscribers($lists = [])
    {
        $lists = count($lists) ? $lists : $this->getAttr('lists');

        foreach ($lists as $id) {
            $list = $this->find($id);
            $this->addSubscribers($list);
        }
    }
    public function removeBulkListSubscribers($lists = [])
    {
        $lists = count($lists) ?  $lists : $this->getAttr('lists');

        foreach ($lists as $list) {
            $this->removeSubscribers(
                $this->find($list)
            );
        }
    }

    public function addSubscribers($list = null, $subscribers = [])
    {
        $list = $list ? $list : $this->model;
        $subscribers = count($subscribers)
            ? $subscribers
            : $this->getAttr('subscribers');

        $chunks = collect($subscribers)->chunk(1000);

        foreach ($chunks as $chunk) {
            $list->subscribers()
                ->syncWithoutDetaching($chunk);
        }

    }
    public function removeSubscribers($list = null, $subscribers = [])
    {
        $subscribers = count($subscribers)
            ? $subscribers
            : $this->getAttr('subscribers');

        $chunks = collect($subscribers)->chunk(1000);

        foreach ($chunks as $chunk) {
            $list->subscribers()->detach($chunk);
        }
    }

}
