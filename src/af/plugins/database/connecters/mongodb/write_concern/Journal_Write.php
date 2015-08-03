<?php
namespace af\plugins\database\connecters\mongodb\write_concern;

/**
 * @class Journal
 *
 * @brief 저널링 기능을 사용하고 있다면 사용할 수 있다. 다른 옵션들의 쓰기 모드는 모두
 *        서버의 메모리에 쓰기가 완료되었는지 확인하지만 Joural_Write는 디스크에 데이터가 써진후에야 응답을
 *        해주기 때문에 느리지만 안정적이다. 서버 크래시가 일어나더라도 데이터를 잃어 버릴 염려가 적어진다.
 *        아주 중요한 데이터에만 사용하도록 한다.
 */
class Journal_Write implements Write_Concern {
  public function get_options() {
    return array('j'=>true);
  }
}
