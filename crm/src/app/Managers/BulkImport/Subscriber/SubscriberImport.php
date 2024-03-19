<?php


namespace App\Managers\BulkImport\Subscriber;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\InstanceCreator;
use App\Managers\BulkImport\BulkImportManager;
use App\Models\Subscriber\Subscriber;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class SubscriberImport implements SubscriberImportContract
{
    protected $import;
    protected $file;
    protected $columns;

    use InstanceCreator;
    public function __construct(BulkImportManager $manager)
    {
        $this->import = $manager;
    }

    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        $this->columns = $this->import->fields($file);
        return $this;
    }

    /**
     *
     * @return GeneralException|array
     */
    public function preview()
    {
        return $this->sanitize(
            $this->file
        );
    }

    public function getFiltered()
    {
        return $this->sanitize($this->file)['filtered'];
    }

    public function getSanitized($subscribers)
    {
        return $this->sanitize($subscribers)['sanitized'];
    }


    private function writableColumns()
    {
        return [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'custom_fields' => []
        ];
    }

    /**
     * @return array
     */
    public function read() : array
    {
        $metas = $this->writableColumns();

        return $this->import->readImported($this->file, function ($row) use ($metas) {
            foreach ($this->columns as $key => $field) {
                if (in_array($field, ['first_name', 'last_name', 'email']))
                    $metas[$field] = $row[$key];
                else
                    $metas['custom_fields'][$field] = $row[$key];
            }
            return $metas;
        });
    }

    /**
     * @param UploadedFile|array|Collection $subscribers
     * @return GeneralException|array
     */
    public function sanitize($subscribers)
    {
        if ($subscribers instanceof UploadedFile) {
            $subscribers = $this
                ->setFile($subscribers)
                ->read();
        }

        if (is_array($subscribers)) {
            $subscribers = collect($subscribers);
        }

        if (!($subscribers instanceof Collection)) {
            return new GeneralException('Type error');
        }

        $subscribers = $subscribers
            ->whereNotIn('email', $this->existedEmails($subscribers));

        $filtered = $this->filterDuplicates($subscribers);
        $sanitized = $subscribers->except(
            $filtered->keys()
        );

        return [
            'filtered' => $filtered->values(),
            'sanitized' => $sanitized->values(),
            'columns' => $this->columns
        ];
    }

    private function existedEmails(Collection $subscribers)
    {
        return Subscriber::query()
            ->whereBrandId(request('brand_id'))
            ->whereIn(
                'email', $subscribers->unique('email')
                ->pluck('email')
                ->filter()
            )
            ->pluck('email');
    }


    private function filterDuplicates(Collection $subscribers)
    {
        $duplicateEmails = $subscribers->groupBy('email')->filter(function ($group) {
            return count($group) > 1;
        })->keys()
            ->unique()
            ->toArray();

        return $subscribers->whereIn('email', array_merge($duplicateEmails, ['email' => '']))->map(function
        ($subscriber) {
            return $this->getErrorMessages($subscriber);
        });
    }

    public function getErrorMessages($subscriber)
    {
        $validated = \Validator::make($subscriber, [
            'email' => 'email|required'
        ])->errors();

        if ($subscriber['email'])
            $validated->add('email', trans('default.duplicated', [
                'name' => 'email'
            ]));

        return array_merge($subscriber, [
            'errorBag' => $validated->toArray()
        ]);
    }

    public function getColumns()
    {
        return $this->columns;
    }
}
