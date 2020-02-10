<?php

class WidgetPacker
{
  //Must be in descending order if added to
  const WIDGET_PACKS = [5000, 2000, 1000, 500, 250];
  private $target = NULL;
  private $orderList = [];
  
  function __construct(int $order) {
    $this->totalOrder = $order;
  }

  public function packWidgets(int $target = NULL)
  {
    if($target === NULL){
      $this->setTarget();
      $target = $this->target;
    } else {
      if($target <= 0 || ($this->getCurrentTotal() > $this->totalOrder)){
        return;
      }
    }

    $widgetOrder = new WidgetOrder();
    foreach(self::WIDGET_PACKS as $key => $widgetPack){
      $isLastPack = ($key === array_key_last(self::WIDGET_PACKS));
      if($widgetPack <= $target || $isLastPack){
        $widgetOrder->size = $widgetPack;
        $widgetOrder->quantity = $this->getMaxQuantity($target, $widgetPack);
        $this->orderList[] = $widgetOrder;
      break;
    }
  }
  
    $target = $this->target - $this->getCurrentTotal();
    $this->packWidgets($target);
  }

  public function printOrder()
  {
    echo "\n--------------------------------------------------------------------\n";
    echo "To fullfill this order of {$this->totalOrder} packs you will need to pack...\n";
    array_walk($this->orderList, function($widgetPack){
      echo "A bundle of {$widgetPack->quantity} {$widgetPack->size} packs\n";
    });
    echo "--------------------------------------------------------------------\n";
  }

  private function setTarget(int $remainingOrder = NULL, int $target = 0)
  {
    if($remainingOrder === NULL){
      $remainingOrder = $this->totalOrder;
    } else {
      if($remainingOrder <= 0){
        $this->target = $target;
        return;
      }
    }

    foreach(self::WIDGET_PACKS as $key => $widgetPack){
      $isLastPack = ($key === array_key_last(self::WIDGET_PACKS));
      if($widgetPack <= $remainingOrder || $isLastPack){
        $target += ($widgetPack * $this->getMaxQuantity($remainingOrder, $widgetPack));
        break;
      }
    }

    $remainingOrder = $this->totalOrder - $target;
    $this->setTarget($remainingOrder, $target);
  }

  private function getMaxQuantity(int $order, int $widgetPack)
  {
    $maxQuantity = floor($order/$widgetPack);
    return $maxQuantity > 0 ? $maxQuantity : 1;
  }

  private function getCurrentTotal()
  {
    $accumulatedOrder = array_reduce($this->orderList, function($accumulator, $item){
      return $accumulator + ($item->size * $item->quantity);
    });
    return $accumulatedOrder;
  }
}

class WidgetOrder 
{
  public $size;
  public $quantity;
}

//Tests
$test1 = new WidgetPacker(1);
$test1->packWidgets();
$test1->printOrder();

$test2 = new WidgetPacker(250);
$test2->packWidgets();
$test2->printOrder();

$test3 = new WidgetPacker(251);
$test3->packWidgets();
$test3->printOrder();

$test4 = new WidgetPacker(501);
$test4->packWidgets();
$test4->printOrder();

$test5 = new WidgetPacker(12001);
$test5->packWidgets();
$test5->printOrder();

$test6 = new WidgetPacker(5999);
$test6->packWidgets();
$test6->printOrder();

$test7 = new WidgetPacker(9753);
$test7->packWidgets();
$test7->printOrder();
