<?php

namespace App\Http\Controllers;

use App\Models\Countries;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestingController extends Controller
{


    public function index() {
        return view("testing.initial", array("transactions" => $this->getMappedTransactions(), "countries" => Countries::all()));
    }


    public function filterTransactions(Request $request) {
        $transactions = $this->getMappedTransactions();
        $filteredItems =  $transactions->filter(function ($transaction) use ($request) {

            if($request->data_type === "chart") {
                if($request->year) {
                    if($transaction->created_at->year !== (int)$request->year) return false;
                }
                if($request->month && $request->month !== "all") {
                    if($transaction->created_at->month !== (int)$request->month) return false;
                }
                if($request->country && $request->country !== "all") {
                    if(strtolower($transaction->invoice_country) !== strtolower($request->country)) return false;
                }
            }
            elseif($request->data_type === "table") {
                $timeStart = (int)$request->start;
                $timeEnd = (int)$request->end;
                if(strtotime($transaction->created_at) < $timeStart || strtotime($transaction->created_at) > $timeEnd) return false;
            }


            return true;
        });


        if(isset($request["format_by_date"]) && $request["format_by_date"]) $filteredItems = $this->mapDataByDate($filteredItems, $request);
        elseif(isset($request["format_country"]) && $request["format_country"]) $filteredItems = $this->mapDataByCountry($filteredItems);
        return response()->json($filteredItems);
    }

    private function mapDataByDate($filteredItems, $request): array {
        if($request->month !== "all") {
            $month = strlen($request->month) === 1 ? 0 . $request->month : $request->month;
            $timeStart = strtotime($request->year . $month . "01");
            $timeEnd = strtotime(date("Y-m-d", $timeStart) . " +1 month");
            $additionKey = "day";
            $dateKey = "Y-m-d";
        }
        else {
            $timeStart = strtotime($request->year . "0101");
            $timeEnd = strtotime(date("Y-m-d", $timeStart) . " +1 year");
            $additionKey = "month";
            $dateKey = "Y-m";
        }

        $endDate = date($dateKey, $timeEnd);
        $currentDate = date($dateKey, $timeStart);
        $dataPointListKeys = array();
        while($currentDate !== $endDate) {
            if(strtotime($currentDate) > strtotime($endDate)) break;
            $dataPointListKeys[] = $currentDate;
            $currentDate = date($dateKey, strtotime($currentDate . " +1 $additionKey"));
        }

        $response = [];
        foreach ($dataPointListKeys as $date) {
            $response[$date] = $filteredItems->filter(function ($item) use($date, $dateKey) {
//                echo json_encode(array(date($dateKey, strtotime($item->created_at)), $date));
                return date($dateKey, strtotime($item->created_at)) == $date;
            });
        }
        return $response;
    }


    private function mapDataByCountry($filteredItems): array {
        if(empty($filteredItems)) return array();
        $collector = $response = array();

        foreach ($filteredItems as $item) {
            $country = empty($item->invoice_country) ? "No country" : $item->invoice_country;
            if(!array_key_exists($country, $collector)) $collector[$country] = array();
            $collector[$country][] = $item;
        }

        $totalInclVat = $totalExclVat = $totalVat = $totalItems = 0;
        foreach ($collector as $countryName => $items) {
            $countryInclVat = $countryExclVat = $countryVat = $countryVatPercentage = 0;
            $countryItems = count($items);

            foreach ($items as $item) {
                $itemExclVat = (float)$item->amount;
//                $itemInclVat = (float)$item->amount;
                $itemVat = 0;

                if(!empty($item->tax_details)) {
                    $taxPercentage = $countryVatPercentage = (float)$item->tax_details->percentage;
                    $itemInclVat = ( ($itemExclVat * (1 + $taxPercentage / 100)) );
                    $itemVat = ( $itemInclVat - $itemExclVat );
//                    $taxAmount = round( ($itemInclVat * ($taxPercentage / 100)) ,2);

//                    $itemExclVat = round($itemInclVat - $taxAmount, 2);
//                    $itemVat = $taxAmount;
                }
                else $itemInclVat = $itemExclVat;

                $countryInclVat += $itemInclVat;
                $countryExclVat += $itemExclVat;
                $countryVat += $itemVat;
            }

            $totalInclVat += $countryInclVat;
            $totalExclVat += $countryExclVat;
            $totalVat += $countryVat;
            $totalItems += $countryItems;
            $response[$countryName] = array(
                "net_sales" => round($countryInclVat, 2),
                "net_sales_excl_vat" => round($countryExclVat, 2),
                "vat" => round($countryVat, 2),
                "total_sales" => $countryItems,
                "vat_percentage" => $countryVatPercentage,
            );
        }

        $response["total"] = array(
            "net_sales" => round($totalInclVat, 2),
            "net_sales_excl_vat" => round($totalExclVat, 2),
            "vat" => round($totalVat, 2),
            "total_sales" => $totalItems,
            "vat_percentage" => 0,
        );
        return $response;
    }



    public function getMappedTransactions() {
        $transactions = Transactions::all();
        return $transactions->map(function ($transaction) {
            $newItem = $transaction;
            $newItem->tax_details = null;
            if(empty($transaction->taxes)) return $newItem;
            $newItem->tax_details = DB::table("tax_rates")->where("id", "=", $transaction->taxes)->first();

            return $newItem;
        });
    }

}
