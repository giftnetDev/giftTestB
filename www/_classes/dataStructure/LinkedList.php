<?
	class Node{
		private $data;
		private $next;

		public function __construct(){
			$this->data=-1;
			$this->next=null;
		}

		public function setData($data){
			$this->data=$data;
		}
		public function getData(){
			return $this->data;
		}
		public function setNext($next){
			$this->next = $next;
        }
        public function getNext(){
            if($this->next != null)
            return $this->next;
        }

    }
    
    class LinkedList{
        private $head;
        private $cnt;

        public function __construct(){
            $this->head=null;
            $this->cnt=0;
        }

        public function getHead(){
            return $this->head;
        }

        public function insertAtFront($data){
            //현재 리스트에 가장 앞에 붙인다.
            if($this->head){//해당리스트에 node가 1개 이상 있을 경우
                $newNode = new Node();
                $newNode->setData($data);
                $newNode->setNext($this->head);
                $this->head =$newNode;
            }
            else{//해당 리스트에 node가 하나도 없을 경우
                $newNode=new Node();
                $newNode->setData($data);
                $this->head=$newNode;
            }
            $this->cnt++;
        }

        public function insertBeforeSpecificNode($data, $target){
            //target앞에 노드에 추가
            if($this->head){ //this->head가 null이 아니면
                $currNode=$this->head;
                $prevNode=null;

                while($currNode->getData()!=$target && $currNode->getNext()!=null){
                    $prevNode=$currNode;
                    $currNode=$currNode->getNext();
                }
                if($currNode->getData()==$target){
                    //원하는 target을 찾았을 경우]
                    $newNode=new Node();
                    $newNode->setData($data);

                    if($prevNode){//head가 currNode가 아닌 경우 즉
                        $prevNode->setNext($newNode);//prevNode의 next를 newNode로 가리키게 한다.
                        $newNode->setNext($currNode);//newNode의 next를 currNode로 가리키게 한다.
                    }
                    else{//head가 currNode인 경우
                        $prevNode=$newNode;
                        $prevNode->setNext($currNode);
                        $this->head=$prevNode;//결국 해당리스트에 가장 앞으로 와야함으로 prevNode, 즉newNode를 
                    }
                }//end of if($currNode->getData()==$target)
                else{
                    //head가 null이라는 이야기는 노드가 하나도 없는 리스트라는 말이다
                    //결국 특정 $target 앞에 Node를 추가한다는 것이 모순이 된다.($target에 해당되는 데이터가 없는것이 당연하기 때문에)
                }
            }
            $this->cnt++;
        }//end of function :insertBeforeSpecificNode($data, $target);

        public function insertAtBack($data){
            //List에 가장 뒤에 붙이기
            $newNode = new Node();
            $newNode->setData($data);

            if($this->head){
                $currNode = $this->head;
                while($currNode->getNext() != null){
                    $currNode=$currNode->getNext();
                }
                $currNode->setNext($newNode);
                // echo "insert_back_node LinkedList.php_91<br>";
            }
            else{
                $this->head=$newNode;
                // echo "insert_first LinkedList.php_95<br>";
            }   
            $this->cnt++;
        }//End of Function insertAtBack($data);

        public function insertAfterSpecificNode($data, $target){
            if($this->head){
                $currNode=$this->head;
                while($currNode->getData()!=$target && $currNode->getNext()!=null){
                    $currNode=$currNode->getNext();
                }
                if($currNode->getData()==$target){
                    $newNode= new Node();
                    $newNode->setData($data);

                    $nextNode=$currNode->getNext();  //$currNode->getNext()의 반환값이 SpecificNode일 수도 있고 NULL일 수도 있다.
                    $newNode->setNext($nextNode);
                    $currNode->setNext($newNode);
                }
            }
            $this->cnt++;
        }//End of Function insertAfterSpecificNode($data, $target);

        public function deleteAtFront(){
            if($this->head){
                $currNode=$this->head;
                $this->head=$currNode->getNext();
                unset($currNode);
                $this->cnt--;
                return true;
            }
            return false;
        }

        public function deleteAtBack(){
            if($this->head){
                $currNode=$this->head;
                $prevNode=null;

                while($currNode->getNext!=null){
                    $prevNode=$currNode;
                    $currNode=$currNode->getNext();
                }

                if($prevNode){//노드가 2개이상이다.
                    unset($currNode);
                    $prevNode->setNext(null);
                }
                else{//노드가 하나밖에 없다.
                    unset($currNode);
                    $this->head=null;
                }
                $this->cnt--;
                return true;
            }
            else{
                //node가 하나도 없다.
                return false;
            }
        }

        public function deleteNode($target){
            if($this->head){
                $currNode=$this->head;
                $prevNode=null;

                while($currNode->getData()!=$target && $currNode->getNext()!=null){
                    $prevNode=$currNode;
                    $currNode=$currNode->getNext();
                }
                if($currNode->getData()==$target){
                    if($prevNode){
                        // echo "2개이상<br>";
                        $prevNode->getNext($currNode->getNext());
                        unset($currNode);
                    }
                    else{//노드가 1개밖에 없거나 $target이 $this->head인 경우
                        // echo"1개?<br>";
                        
                        $this->head = $currNode->getNext();//$currNode가 $this->head이므로 $this->head의 정보($currNode)를 지워야 하기때문에 $currNode 뒤에 있는 NODE로 $this->head를 연결한다.
                        unset($currNode);
                    }
                    // echo "Delete_NODE!!!<br>";
                    $this->cnt--;
                    return true;
                }
                echo "Delete Fail<br>";
                return false;
            }
        }//End of function deleteNode($target);

        public function printNode(){
            $currNode=$this->head;
            while($currNode != null){
                echo "->";
                echo $currNode->getData();
                $currNode = $currNode->getNext();
                
            }
            echo "<br>";
        }// End of Function printNode();

        public function deleteAll(){
            while(true){
                if($this->deleteAtFront()==false) break;
            }
            $this->cnt=0;
        }
        public function sizeOfList(){
            return $this->cnt;
        }

    }//end of class
    


    //$list = new LinkedList();

?>