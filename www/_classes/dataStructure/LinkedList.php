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
            //���� ����Ʈ�� ���� �տ� ���δ�.
            if($this->head){//�ش縮��Ʈ�� node�� 1�� �̻� ���� ���
                $newNode = new Node();
                $newNode->setData($data);
                $newNode->setNext($this->head);
                $this->head =$newNode;
            }
            else{//�ش� ����Ʈ�� node�� �ϳ��� ���� ���
                $newNode=new Node();
                $newNode->setData($data);
                $this->head=$newNode;
            }
            $this->cnt++;
        }

        public function insertBeforeSpecificNode($data, $target){
            //target�տ� ��忡 �߰�
            if($this->head){ //this->head�� null�� �ƴϸ�
                $currNode=$this->head;
                $prevNode=null;

                while($currNode->getData()!=$target && $currNode->getNext()!=null){
                    $prevNode=$currNode;
                    $currNode=$currNode->getNext();
                }
                if($currNode->getData()==$target){
                    //���ϴ� target�� ã���� ���]
                    $newNode=new Node();
                    $newNode->setData($data);

                    if($prevNode){//head�� currNode�� �ƴ� ��� ��
                        $prevNode->setNext($newNode);//prevNode�� next�� newNode�� ����Ű�� �Ѵ�.
                        $newNode->setNext($currNode);//newNode�� next�� currNode�� ����Ű�� �Ѵ�.
                    }
                    else{//head�� currNode�� ���
                        $prevNode=$newNode;
                        $prevNode->setNext($currNode);
                        $this->head=$prevNode;//�ᱹ �ش縮��Ʈ�� ���� ������ �;������� prevNode, ��newNode�� 
                    }
                }//end of if($currNode->getData()==$target)
                else{
                    //head�� null�̶�� �̾߱�� ��尡 �ϳ��� ���� ����Ʈ��� ���̴�
                    //�ᱹ Ư�� $target �տ� Node�� �߰��Ѵٴ� ���� ����� �ȴ�.($target�� �ش�Ǵ� �����Ͱ� ���°��� �翬�ϱ� ������)
                }
            }
            $this->cnt++;
        }//end of function :insertBeforeSpecificNode($data, $target);

        public function insertAtBack($data){
            //List�� ���� �ڿ� ���̱�
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

                    $nextNode=$currNode->getNext();  //$currNode->getNext()�� ��ȯ���� SpecificNode�� ���� �ְ� NULL�� ���� �ִ�.
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

                if($prevNode){//��尡 2���̻��̴�.
                    unset($currNode);
                    $prevNode->setNext(null);
                }
                else{//��尡 �ϳ��ۿ� ����.
                    unset($currNode);
                    $this->head=null;
                }
                $this->cnt--;
                return true;
            }
            else{
                //node�� �ϳ��� ����.
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
                        // echo "2���̻�<br>";
                        $prevNode->getNext($currNode->getNext());
                        unset($currNode);
                    }
                    else{//��尡 1���ۿ� ���ų� $target�� $this->head�� ���
                        // echo"1��?<br>";
                        
                        $this->head = $currNode->getNext();//$currNode�� $this->head�̹Ƿ� $this->head�� ����($currNode)�� ������ �ϱ⶧���� $currNode �ڿ� �ִ� NODE�� $this->head�� �����Ѵ�.
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