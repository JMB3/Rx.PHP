<?php

namespace Rx\Functional\Operator;


use Rx\Functional\FunctionalTestCase;
use Rx\Observable\ReturnObservable;


class TakeTest extends FunctionalTestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_on_negative_amounts() 
    {
        $observable = new ReturnObservable(42);
        $observable->take(-1);
    }

    /**
     * @test
     */
    public function it_passes_on_complete()
    {
        $xs = $this->createHotObservable(array(
            onNext(300, 21),
            onNext(500, 42),
            onNext(800, 84),
            onCompleted(820),
        ));

        $results = $this->scheduler->startWithCreate(function() use ($xs) {
            return $xs->take(5);
        });

        $this->assertMessages(array(
            onNext(300, 21),
            onNext(500, 42),
            onNext(800, 84),
            onCompleted(820),
        ), $results->getMessages());
    }

    /**
     * @test
     */
    public function it_calls_on_complete_after_last_value()
    {
        $scheduler = $this->createTestScheduler();
        $xs        = $this->createHotObservable(array(
            onNext(300, 21),
            onNext(500, 42),
            onNext(800, 84),
            onCompleted(820),
        ));

        $results = $this->scheduler->startWithCreate(function() use ($xs) {
            return $xs->take(2);
        });

        $this->assertMessages(array(
            onNext(300, 21),
            onNext(500, 42),
            onCompleted(500),
        ), $results->getMessages());
    }

    /**
     * @test
     */
    public function take_zero_calls_on_completed()
    {
        $scheduler = $this->createTestScheduler();
        $xs        = $this->createHotObservable(array(
            onNext(300, 21),
            onNext(500, 42),
            onNext(800, 84),
            onCompleted(820),
        ));

        $results = $this->scheduler->startWithCreate(function() use ($xs) {
            return $xs->take(0);
        });

        $this->assertMessages(array(
            onCompleted(201),
        ), $results->getMessages());
    }
}
