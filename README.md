PHP(Slim) 학습 프로젝트 
=============

숙제 겸 재미삼아 진행하는 학습 프로젝트.

Spec
-------------
* API Server(자세한 사항은 Dockerfile 참조)
    * ubuntu 18.04LTS
    * PHP 7.4+
    * Slim 4.+
    * Nginx
    * ETC
* DB Server
    * MySQL 8(docker image latest)

Features(+TODO)
-------------
- [x] 기본 데이터베이스 생성
- [x] 회원 등록 API
- [x] 회원 목록 API
- [x] 회원 상세 API
- [x] 로그인 API(with jwt)
- [x] 회원 검색 API (email, name + pagination)
- [x] 회원 목록 API 페이지네이션 -> 추후 Feature 삭제하는 것으로 대체
- [ ] 조회 쿼리 캐싱
- [ ] Class Autoload 할 수 있게 변경
- [ ] Code Cleanup

Initialize
-------------
1. Host 설정(해당 프로젝트는 study.wrong.tips라는 도메인으로 테스트함)
> 만약 해당 도메인이 아니라 다른 도메인으로 테스트하려면 /docker/conf/nginx.conf에서 도메인 정보 수정
2. start.sh 스크립트 실행(MySQL Database, API Server Container Start)
3. http://{host}:8080/를 호출하면 자동으로 Schema와 Table 생성됨


API Document
-------------
전반적인 사용 방법은 Postman 문서 참고

https://documenter.getpostman.com/view/631821/SzfDvPxZ

* [GET] /
    * 초기 initialize API
    * 1회만 호출하면 ok
* [POST] /user
    * 사용자 등록 API
        * json body로 name, password, nickname, cellphone, email, gender(옵션) 전달하면 사용자 등록
        * 오류 발생시 오류 데이터 전체 리턴(검증 정책내용이 포함되어있음)
* [POST] /auth/signin
    * 사용자 로그인 API
        * json body로 email과 password 보내면 id_token 반환됨
        * 이후 header에 ID-TOKEN을 추가해야 사용자 목록 및 사용자 조회 API를 호출할 수 있다.
* [GET] /user
    * 사용자 목록 API
        * Header에 ID-TOKEN 필요
    * 사용자 검색 API
        * Header에 ID-TOKEN 필요
        * Query param 필요
            * k => keyword(검색어, 1자 이상 문자열)
            * t => target(대상, [name, email])
            * ty => type(검색방법, [match, startPart])
            * o => offset(위치, 0부터 시작)
            * c => count(개수, 1~15사이 정수)
* [GET] /user/{id}
    * 사용자 조회 API
        * Header에 ID-TOKEN 필요
        
        
