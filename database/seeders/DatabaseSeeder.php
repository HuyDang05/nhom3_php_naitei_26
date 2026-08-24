<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Department;
use App\Models\ServiceCategory;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $citizens = $this->seedSystemAccounts();
        [$departments, $staffByDepartment] = $this->seedDepartmentsAndAssignedPersonnel();

        $this->seedUnassignedPersonnel();

        $categories = $this->seedServiceCategories();
        $services = $this->seedServiceTypes($categories, $departments, $staffByDepartment);

        $this->seedApplications($citizens, $services, $staffByDepartment);
    }

    /** @return Collection<string, User> */
    private function seedSystemAccounts(): Collection
    {
        $this->createUser(UserRole::SuperAdmin, [
            'name' => 'Quản trị viên cấp cao Demo',
            'email' => 'admin@example.test',
            'date_of_birth' => '1985-02-12',
            'gender' => 'Nam',
            'phone' => '0901000000',
            'address' => 'Trung tâm Phục vụ Hành chính công Thành phố',
        ]);

        return collect([
            [
                'name' => 'Công dân Demo 1',
                'email' => 'citizen1@example.test',
                'citizen_id' => '001090000001',
                'date_of_birth' => '1990-05-16',
                'gender' => 'Nam',
                'phone' => '0912000001',
                'address' => '12 phố Tràng Thi, phường Cửa Nam, Thành phố Hà Nội',
            ],
            [
                'name' => 'Công dân Demo 2',
                'email' => 'citizen2@example.test',
                'citizen_id' => '001192000002',
                'date_of_birth' => '1992-09-24',
                'gender' => 'Nữ',
                'phone' => '0912000002',
                'address' => '35 phố Huế, phường Hai Bà Trưng, Thành phố Hà Nội',
            ],
        ])->mapWithKeys(function (array $citizen): array {
            $user = $this->createUser(UserRole::Citizen, $citizen);

            return [$citizen['email'] => $user];
        });
    }

    /**
     * @return array{0: Collection<string, Department>, 1: Collection<string, Collection<int, User>>}
     */
    private function seedDepartmentsAndAssignedPersonnel(): array
    {
        $definitions = [
            [
                'name' => 'Phòng Hộ tịch và Chứng thực',
                'code' => 'HTCT',
                'address' => 'Tầng 1, Trung tâm Phục vụ Hành chính công Thành phố',
                'manager' => ['name' => 'Nguyễn Minh Anh', 'email' => 'manager@example.test', 'date_of_birth' => '1984-03-18', 'gender' => 'Nam', 'phone' => '0901000001'],
                'staff' => [
                    ['name' => 'Trần Thu Hương', 'email' => 'staff1@example.test', 'date_of_birth' => '1992-07-11', 'gender' => 'Nữ', 'phone' => '0902000001'],
                    ['name' => 'Lê Quang Huy', 'email' => 'staff2@example.test', 'date_of_birth' => '1990-12-05', 'gender' => 'Nam', 'phone' => '0902000002'],
                ],
            ],
            [
                'name' => 'Phòng Lao động và An sinh xã hội',
                'code' => 'LDAS',
                'address' => 'Tầng 2, Trung tâm Phục vụ Hành chính công Thành phố',
                'manager' => ['name' => 'Trần Quốc Bảo', 'email' => 'manager2@example.test', 'date_of_birth' => '1982-06-20', 'gender' => 'Nam', 'phone' => '0901000002'],
                'staff' => [
                    ['name' => 'Phạm Thị Mai', 'email' => 'staff3@example.test', 'date_of_birth' => '1991-02-14', 'gender' => 'Nữ', 'phone' => '0902000003'],
                    ['name' => 'Đặng Văn Thành', 'email' => 'staff4@example.test', 'date_of_birth' => '1989-08-29', 'gender' => 'Nam', 'phone' => '0902000004'],
                ],
            ],
            [
                'name' => 'Phòng Giáo dục và Đào tạo',
                'code' => 'GDDT',
                'address' => 'Tầng 3, Trung tâm Phục vụ Hành chính công Thành phố',
                'manager' => ['name' => 'Lê Thu Hà', 'email' => 'manager3@example.test', 'date_of_birth' => '1983-11-09', 'gender' => 'Nữ', 'phone' => '0901000003'],
                'staff' => [
                    ['name' => 'Nguyễn Thị Ngọc', 'email' => 'staff5@example.test', 'date_of_birth' => '1993-04-23', 'gender' => 'Nữ', 'phone' => '0902000005'],
                    ['name' => 'Hoàng Minh Đức', 'email' => 'staff6@example.test', 'date_of_birth' => '1990-10-17', 'gender' => 'Nam', 'phone' => '0902000006'],
                ],
            ],
            [
                'name' => 'Phòng Y tế',
                'code' => 'YTE',
                'address' => 'Tầng 4, Trung tâm Phục vụ Hành chính công Thành phố',
                'manager' => ['name' => 'Phạm Hoàng Nam', 'email' => 'manager4@example.test', 'date_of_birth' => '1981-01-27', 'gender' => 'Nam', 'phone' => '0901000004'],
                'staff' => [
                    ['name' => 'Vũ Thanh Thảo', 'email' => 'staff7@example.test', 'date_of_birth' => '1994-06-08', 'gender' => 'Nữ', 'phone' => '0902000007'],
                    ['name' => 'Đỗ Hải Long', 'email' => 'staff8@example.test', 'date_of_birth' => '1988-09-12', 'gender' => 'Nam', 'phone' => '0902000008'],
                ],
            ],
            [
                'name' => 'Phòng Quản lý đô thị và Xây dựng',
                'code' => 'QLXD',
                'address' => 'Tầng 5, Trung tâm Phục vụ Hành chính công Thành phố',
                'manager' => ['name' => 'Vũ Ngọc Lan', 'email' => 'manager5@example.test', 'date_of_birth' => '1985-05-03', 'gender' => 'Nữ', 'phone' => '0901000005'],
                'staff' => [
                    ['name' => 'Bùi Trung Kiên', 'email' => 'staff9@example.test', 'date_of_birth' => '1989-03-19', 'gender' => 'Nam', 'phone' => '0902000009'],
                    ['name' => 'Nguyễn Diệu Linh', 'email' => 'staff10@example.test', 'date_of_birth' => '1992-11-28', 'gender' => 'Nữ', 'phone' => '0902000010'],
                ],
            ],
            [
                'name' => 'Phòng Tài nguyên và Môi trường',
                'code' => 'TNMT',
                'address' => 'Tầng 6, Trung tâm Phục vụ Hành chính công Thành phố',
                'manager' => ['name' => 'Đỗ Thành Công', 'email' => 'manager6@example.test', 'date_of_birth' => '1980-08-15', 'gender' => 'Nam', 'phone' => '0901000006'],
                'staff' => [
                    ['name' => 'Trịnh Tuấn Anh', 'email' => 'staff11@example.test', 'date_of_birth' => '1991-01-06', 'gender' => 'Nam', 'phone' => '0902000011'],
                    ['name' => 'Phan Thu Trang', 'email' => 'staff12@example.test', 'date_of_birth' => '1993-07-30', 'gender' => 'Nữ', 'phone' => '0902000012'],
                ],
            ],
            [
                'name' => 'Phòng Tài chính - Kế hoạch',
                'code' => 'TCKH',
                'address' => 'Tầng 7, Trung tâm Phục vụ Hành chính công Thành phố',
                'manager' => ['name' => 'Bùi Khánh Linh', 'email' => 'manager7@example.test', 'date_of_birth' => '1986-04-21', 'gender' => 'Nữ', 'phone' => '0901000007'],
                'staff' => [
                    ['name' => 'Lương Quốc Việt', 'email' => 'staff13@example.test', 'date_of_birth' => '1988-12-22', 'gender' => 'Nam', 'phone' => '0902000013'],
                    ['name' => 'Mai Hồng Nhung', 'email' => 'staff14@example.test', 'date_of_birth' => '1994-02-10', 'gender' => 'Nữ', 'phone' => '0902000014'],
                ],
            ],
            [
                'name' => 'Phòng Văn hóa và Thông tin',
                'code' => 'VHTT',
                'address' => 'Tầng 8, Trung tâm Phục vụ Hành chính công Thành phố',
                'manager' => ['name' => 'Hoàng Đức Long', 'email' => 'manager8@example.test', 'date_of_birth' => '1982-10-31', 'gender' => 'Nam', 'phone' => '0901000008'],
                'staff' => [
                    ['name' => 'Đinh Bảo Châu', 'email' => 'staff15@example.test', 'date_of_birth' => '1992-05-25', 'gender' => 'Nữ', 'phone' => '0902000015'],
                    ['name' => 'Nguyễn Duy Khánh', 'email' => 'staff16@example.test', 'date_of_birth' => '1990-07-04', 'gender' => 'Nam', 'phone' => '0902000016'],
                ],
            ],
        ];

        $departments = collect();
        $staffByDepartment = collect();

        foreach ($definitions as $definition) {
            $manager = $this->createUser(UserRole::Manager, [
                ...$definition['manager'],
                'address' => $definition['address'],
            ]);
            $staff = collect($definition['staff'])->map(fn (array $person): User => $this->createUser(
                UserRole::Staff,
                [...$person, 'address' => $definition['address']],
            ));

            $department = Department::query()->create([
                'name' => $definition['name'],
                'code' => $definition['code'],
                'address' => $definition['address'],
                'leader_id' => $manager->id,
            ]);
            $department->users()->attach([$manager->id, ...$staff->pluck('id')->all()]);

            $departments->put($definition['code'], $department);
            $staffByDepartment->put($definition['code'], $staff);
        }

        return [$departments, $staffByDepartment];
    }

    private function seedUnassignedPersonnel(): void
    {
        $managers = [
            ['name' => 'Nguyễn Thanh Bình', 'email' => 'manager9@example.test', 'date_of_birth' => '1984-09-07', 'gender' => 'Nam', 'phone' => '0901000009'],
            ['name' => 'Trần Hải Yến', 'email' => 'manager10@example.test', 'date_of_birth' => '1987-02-16', 'gender' => 'Nữ', 'phone' => '0901000010'],
            ['name' => 'Lê Văn Phúc', 'email' => 'manager11@example.test', 'date_of_birth' => '1983-07-24', 'gender' => 'Nam', 'phone' => '0901000011'],
            ['name' => 'Phạm Ngọc Mai', 'email' => 'manager12@example.test', 'date_of_birth' => '1986-12-13', 'gender' => 'Nữ', 'phone' => '0901000012'],
            ['name' => 'Vũ Quang Vinh', 'email' => 'manager13@example.test', 'date_of_birth' => '1981-04-30', 'gender' => 'Nam', 'phone' => '0901000013'],
        ];
        $staff = [
            ['name' => 'Ngô Thị Hà', 'email' => 'staff17@example.test', 'date_of_birth' => '1994-01-15', 'gender' => 'Nữ', 'phone' => '0902000017'],
            ['name' => 'Dương Minh Tú', 'email' => 'staff18@example.test', 'date_of_birth' => '1991-06-26', 'gender' => 'Nam', 'phone' => '0902000018'],
            ['name' => 'Hà Phương Anh', 'email' => 'staff19@example.test', 'date_of_birth' => '1993-10-09', 'gender' => 'Nữ', 'phone' => '0902000019'],
            ['name' => 'Đào Quốc Hưng', 'email' => 'staff20@example.test', 'date_of_birth' => '1989-05-18', 'gender' => 'Nam', 'phone' => '0902000020'],
            ['name' => 'Tạ Thu Uyên', 'email' => 'staff21@example.test', 'date_of_birth' => '1995-03-02', 'gender' => 'Nữ', 'phone' => '0902000021'],
            ['name' => 'Cao Đức Mạnh', 'email' => 'staff22@example.test', 'date_of_birth' => '1990-08-14', 'gender' => 'Nam', 'phone' => '0902000022'],
            ['name' => 'Lý Ngọc Ánh', 'email' => 'staff23@example.test', 'date_of_birth' => '1992-12-20', 'gender' => 'Nữ', 'phone' => '0902000023'],
        ];

        foreach ($managers as $manager) {
            $this->createUser(UserRole::Manager, [...$manager, 'address' => 'Danh sách nhân sự quản lý chờ phân công']);
        }
        foreach ($staff as $person) {
            $this->createUser(UserRole::Staff, [...$person, 'address' => 'Danh sách nhân viên chờ phân công']);
        }
    }

    /** @return Collection<string, ServiceCategory> */
    private function seedServiceCategories(): Collection
    {
        return collect([
            ['name' => 'Hành chính, hộ tịch và chứng thực', 'code' => 'ADMINISTRATION', 'description' => 'Các thủ tục hộ tịch, xác nhận thông tin và chứng thực giấy tờ, chữ ký của công dân.'],
            ['name' => 'Giáo dục và đào tạo', 'code' => 'EDUCATION', 'description' => 'Các thủ tục tuyển sinh, học tập và quản lý giáo dục.'],
            ['name' => 'Y tế và chăm sóc sức khỏe', 'code' => 'HEALTHCARE', 'description' => 'Các thủ tục về chăm sóc sức khỏe, an toàn thực phẩm và chính sách y tế.'],
            ['name' => 'Xây dựng', 'code' => 'CONSTRUCTION', 'description' => 'Các thủ tục cấp phép xây dựng, sửa chữa và quản lý công trình.'],
            ['name' => 'Đất đai và tài nguyên', 'code' => 'NATURAL_RESOURCES', 'description' => 'Các thủ tục về hồ sơ địa chính, quyền sử dụng đất và khai thác thông tin đất đai.'],
            ['name' => 'Lao động và việc làm', 'code' => 'LABOR_EMPLOYMENT', 'description' => 'Các thủ tục về việc làm, giấy phép lao động và hỗ trợ người lao động.'],
            ['name' => 'Bảo trợ xã hội', 'code' => 'SOCIAL_WELFARE', 'description' => 'Các chính sách trợ giúp thường xuyên, đột xuất và hỗ trợ mai táng cho đối tượng bảo trợ.'],
            ['name' => 'Người có công', 'code' => 'MERITORIOUS_SERVICES', 'description' => 'Các thủ tục xác nhận và giải quyết chế độ ưu đãi đối với người có công và thân nhân.'],
            ['name' => 'Quy hoạch và kiến trúc', 'code' => 'URBAN_PLANNING', 'description' => 'Các thủ tục cung cấp thông tin quy hoạch và chấp thuận phương án kiến trúc, tổng mặt bằng.'],
            ['name' => 'Môi trường', 'code' => 'ENVIRONMENT', 'description' => 'Các thủ tục đăng ký, thẩm định và cấp phép liên quan đến bảo vệ môi trường.'],
            ['name' => 'Đăng ký kinh doanh', 'code' => 'BUSINESS_REGISTRATION', 'description' => 'Các thủ tục thành lập và thay đổi nội dung đăng ký hộ kinh doanh.'],
            ['name' => 'Đầu tư và hỗ trợ doanh nghiệp', 'code' => 'INVESTMENT_SUPPORT', 'description' => 'Các thủ tục đăng ký dự án đầu tư và tiếp cận chương trình hỗ trợ doanh nghiệp.'],
            ['name' => 'Văn hóa, thể thao và du lịch', 'code' => 'CULTURE_SPORTS_TOURISM', 'description' => 'Các thủ tục cấp phép hoạt động văn hóa, biểu diễn, thể thao và du lịch.'],
            ['name' => 'Thông tin và truyền thông', 'code' => 'INFORMATION_COMMUNICATIONS', 'description' => 'Các thủ tục về xuất bản, thông tin điện tử và hoạt động truyền thông.'],
            ['name' => 'Chính sách học phí', 'code' => 'EDUCATION_SUPPORT', 'description' => 'Các thủ tục miễn, giảm học phí và hỗ trợ chi phí học tập cho người học.'],
        ])->mapWithKeys(function (array $category): array {
            $model = ServiceCategory::query()->create($category);

            return [$category['code'] => $model];
        });
    }

    /**
     * @param  Collection<string, ServiceCategory>  $categories
     * @param  Collection<string, Department>  $departments
     * @param  Collection<string, Collection<int, User>>  $staffByDepartment
     * @return Collection<string, ServiceType>
     */
    private function seedServiceTypes(Collection $categories, Collection $departments, Collection $staffByDepartment): Collection
    {
        $definitions = $this->serviceDefinitions();
        $services = collect();

        foreach ($definitions as $definition) {
            $categoryCode = $definition['category_code'];
            $departmentCode = $definition['department_code'];
            $service = ServiceType::query()->create([
                'category_id' => $categories->get($categoryCode)->id,
                'responsible_department_id' => $departments->get($departmentCode)->id,
                'name' => $definition['name'],
                'code' => $definition['code'],
                'description' => $definition['description'],
                'requirements' => $definition['requirements'],
                'form_schema' => $definition['form_schema'],
                'document_requirements' => $definition['document_requirements'],
                'processing_time_days' => $definition['processing_time_days'],
                'fee' => $definition['fee'],
                'is_active' => true,
            ]);

            $service->staff()->attach($staffByDepartment->get($departmentCode)->pluck('id')->all());
            $services->put($definition['code'], $service);
        }

        return $services;
    }

    /** @return array<int, array<string, mixed>> */
    private function serviceDefinitions(): array
    {
        return [
            $this->service('ADMINISTRATION', 'HTCT', 'CIVIL_STATUS_CERTIFICATE', 'Cấp giấy xác nhận thông tin hộ tịch', 'Cấp văn bản xác nhận thông tin hộ tịch đã đăng ký và đang được lưu trong cơ sở dữ liệu hộ tịch.', 'Người yêu cầu có thông tin hộ tịch hợp lệ và xuất trình giấy tờ tùy thân còn hiệu lực.', [['name' => 'purpose', 'label' => 'Mục đích xin xác nhận', 'type' => 'text', 'required' => true]], [['code' => 'citizen_id_copy', 'label' => 'Bản sao căn cước công dân', 'required' => true, 'type' => 'pdf']], 3, 0),
            $this->service('ADMINISTRATION', 'HTCT', 'CERTIFIED_COPY_FROM_ORIGINAL', 'Chứng thực bản sao từ bản chính', 'Chứng thực bản sao giấy tờ, văn bản là đúng với bản chính do cơ quan có thẩm quyền cấp.', 'Người yêu cầu phải cung cấp bản chính còn nguyên vẹn, không bị tẩy xóa hoặc sửa chữa.', [['name' => 'copy_quantity', 'label' => 'Số lượng bản sao cần chứng thực', 'type' => 'number', 'required' => true]], [['code' => 'original_document', 'label' => 'Bản chụp giấy tờ gốc', 'required' => true, 'type' => 'mixed']], 1, 8000),
            $this->service('EDUCATION', 'GDDT', 'PUBLIC_SCHOOL_ENROLLMENT', 'Đăng ký nhập học trường công lập', 'Đăng ký nhập học trực tuyến cho học sinh thuộc tuyến tuyển sinh của trường công lập.', 'Học sinh đáp ứng độ tuổi, địa bàn và điều kiện tuyển sinh theo thông báo của cơ sở giáo dục.', [['name' => 'student_name', 'label' => 'Họ và tên học sinh', 'type' => 'text', 'required' => true], ['name' => 'school_name', 'label' => 'Trường đăng ký', 'type' => 'text', 'required' => true]], [['code' => 'birth_certificate', 'label' => 'Bản sao giấy khai sinh', 'required' => true, 'type' => 'pdf'], ['code' => 'residence_confirmation', 'label' => 'Giấy xác nhận cư trú', 'required' => true, 'type' => 'pdf']], 5, 0),
            $this->service('EDUCATION', 'GDDT', 'SCHOOL_TRANSFER_REQUEST', 'Đề nghị chuyển trường cho học sinh', 'Tiếp nhận đề nghị chuyển trường giữa các cơ sở giáo dục phổ thông theo đúng thẩm quyền.', 'Học sinh có lý do chuyển trường chính đáng và được trường chuyển đi xác nhận tình trạng học tập.', [['name' => 'current_school', 'label' => 'Trường đang học', 'type' => 'text', 'required' => true], ['name' => 'target_school', 'label' => 'Trường đề nghị chuyển đến', 'type' => 'text', 'required' => true]], [['code' => 'school_record', 'label' => 'Học bạ hoặc bảng kết quả học tập', 'required' => true, 'type' => 'pdf'], ['code' => 'transfer_application', 'label' => 'Đơn xin chuyển trường', 'required' => true, 'type' => 'pdf']], 5, 0),
            $this->service('HEALTHCARE', 'YTE', 'HEALTHCARE_SUPPORT_REGISTRATION', 'Đăng ký hỗ trợ chăm sóc sức khỏe', 'Đăng ký hưởng chính sách hỗ trợ chăm sóc sức khỏe dành cho đối tượng đủ điều kiện.', 'Người đăng ký thuộc nhóm đối tượng được hưởng hỗ trợ theo quy định hiện hành.', [['name' => 'support_reason', 'label' => 'Lý do đề nghị hỗ trợ', 'type' => 'text', 'required' => true]], [['code' => 'citizen_id_copy', 'label' => 'Bản sao căn cước công dân', 'required' => true, 'type' => 'pdf'], ['code' => 'eligibility_document', 'label' => 'Giấy tờ chứng minh đối tượng', 'required' => true, 'type' => 'pdf']], 7, 0),
            $this->service('HEALTHCARE', 'YTE', 'FOOD_SAFETY_CERTIFICATE', 'Cấp giấy chứng nhận cơ sở đủ điều kiện an toàn thực phẩm', 'Thẩm định và cấp giấy chứng nhận an toàn thực phẩm cho cơ sở kinh doanh dịch vụ ăn uống.', 'Cơ sở có địa điểm, trang thiết bị, quy trình chế biến và nhân sự đáp ứng quy định an toàn thực phẩm.', [['name' => 'business_name', 'label' => 'Tên cơ sở kinh doanh', 'type' => 'text', 'required' => true], ['name' => 'business_address', 'label' => 'Địa chỉ cơ sở', 'type' => 'text', 'required' => true]], [['code' => 'business_registration', 'label' => 'Giấy chứng nhận đăng ký kinh doanh', 'required' => true, 'type' => 'pdf'], ['code' => 'facility_layout', 'label' => 'Sơ đồ mặt bằng cơ sở', 'required' => true, 'type' => 'pdf']], 15, 500000),
            $this->service('CONSTRUCTION', 'QLXD', 'CONSTRUCTION_PERMIT', 'Cấp giấy phép xây dựng', 'Tiếp nhận và thẩm định hồ sơ đề nghị cấp giấy phép xây dựng công trình hoặc nhà ở riêng lẻ.', 'Công trình phù hợp quy hoạch, mục đích sử dụng đất và các quy định hiện hành về xây dựng.', [['name' => 'construction_area', 'label' => 'Diện tích xây dựng (m²)', 'type' => 'number', 'required' => true], ['name' => 'construction_address', 'label' => 'Địa điểm xây dựng', 'type' => 'text', 'required' => true]], [['code' => 'land_use_certificate', 'label' => 'Giấy chứng nhận quyền sử dụng đất', 'required' => true, 'type' => 'pdf'], ['code' => 'design_drawing', 'label' => 'Bản vẽ thiết kế', 'required' => true, 'type' => 'pdf']], 15, 100000),
            $this->service('CONSTRUCTION', 'QLXD', 'HOUSE_REPAIR_PERMIT', 'Cấp phép sửa chữa, cải tạo nhà ở', 'Thẩm định hồ sơ sửa chữa hoặc cải tạo nhà ở có làm thay đổi kết cấu chịu lực, công năng hoặc mặt ngoài công trình.', 'Người đề nghị có quyền sử dụng hợp pháp công trình và phương án sửa chữa bảo đảm an toàn.', [['name' => 'repair_scope', 'label' => 'Phạm vi sửa chữa, cải tạo', 'type' => 'text', 'required' => true]], [['code' => 'ownership_document', 'label' => 'Giấy tờ chứng minh quyền sở hữu công trình', 'required' => true, 'type' => 'pdf'], ['code' => 'repair_design', 'label' => 'Bản vẽ hiện trạng và phương án sửa chữa', 'required' => true, 'type' => 'pdf']], 12, 75000),
            $this->service('NATURAL_RESOURCES', 'TNMT', 'LAND_RECORD_INFORMATION', 'Yêu cầu cung cấp thông tin hồ sơ đất đai', 'Cung cấp thông tin lưu trữ về thửa đất và quyền sử dụng đất theo yêu cầu hợp lệ.', 'Người yêu cầu xác định rõ thửa đất, địa chỉ và mục đích khai thác thông tin.', [['name' => 'land_lot_number', 'label' => 'Số thửa đất', 'type' => 'text', 'required' => true], ['name' => 'map_sheet_number', 'label' => 'Số tờ bản đồ', 'type' => 'text', 'required' => true]], [['code' => 'citizen_id_copy', 'label' => 'Bản sao căn cước công dân', 'required' => true, 'type' => 'pdf']], 10, 50000),
            $this->service('NATURAL_RESOURCES', 'TNMT', 'FIRST_LAND_USE_CERTIFICATE', 'Cấp lần đầu giấy chứng nhận quyền sử dụng đất', 'Đăng ký và cấp lần đầu giấy chứng nhận quyền sử dụng đất, quyền sở hữu tài sản gắn liền với đất.', 'Thửa đất đang được sử dụng ổn định, không có tranh chấp và đáp ứng điều kiện cấp giấy chứng nhận.', [['name' => 'land_address', 'label' => 'Địa chỉ thửa đất', 'type' => 'text', 'required' => true], ['name' => 'land_area', 'label' => 'Diện tích thửa đất (m²)', 'type' => 'number', 'required' => true]], [['code' => 'land_origin_documents', 'label' => 'Giấy tờ chứng minh nguồn gốc sử dụng đất', 'required' => true, 'type' => 'pdf'], ['code' => 'land_status_diagram', 'label' => 'Sơ đồ hiện trạng thửa đất', 'required' => true, 'type' => 'pdf']], 30, 100000),
            $this->service('LABOR_EMPLOYMENT', 'LDAS', 'JOB_PLACEMENT_SUPPORT', 'Đề nghị hỗ trợ giới thiệu việc làm', 'Tiếp nhận nhu cầu tìm việc và kết nối người lao động với đơn vị tuyển dụng phù hợp.', 'Người lao động trong độ tuổi lao động, có nhu cầu tìm việc và cung cấp thông tin nghề nghiệp trung thực.', [['name' => 'desired_position', 'label' => 'Vị trí việc làm mong muốn', 'type' => 'text', 'required' => true], ['name' => 'expected_salary', 'label' => 'Mức lương mong muốn', 'type' => 'number', 'required' => false]], [['code' => 'resume', 'label' => 'Sơ yếu lý lịch hoặc CV', 'required' => true, 'type' => 'pdf'], ['code' => 'qualification_documents', 'label' => 'Văn bằng, chứng chỉ liên quan', 'required' => false, 'type' => 'pdf']], 5, 0),
            $this->service('LABOR_EMPLOYMENT', 'LDAS', 'FOREIGN_WORK_PERMIT_REISSUE', 'Cấp lại giấy phép lao động cho người lao động nước ngoài', 'Cấp lại giấy phép lao động còn thời hạn khi bị mất, hỏng hoặc thay đổi thông tin theo quy định.', 'Người lao động có giấy phép đã được cấp và thuộc một trong các trường hợp được cấp lại.', [['name' => 'permit_number', 'label' => 'Số giấy phép lao động đã cấp', 'type' => 'text', 'required' => true], ['name' => 'reissue_reason', 'label' => 'Lý do đề nghị cấp lại', 'type' => 'text', 'required' => true]], [['code' => 'employer_request', 'label' => 'Văn bản đề nghị của người sử dụng lao động', 'required' => true, 'type' => 'pdf'], ['code' => 'current_permit_evidence', 'label' => 'Giấy phép cũ hoặc xác nhận mất giấy phép', 'required' => true, 'type' => 'pdf']], 3, 450000),
            $this->service('SOCIAL_WELFARE', 'LDAS', 'REGULAR_SOCIAL_ASSISTANCE', 'Đề nghị hưởng trợ cấp xã hội hằng tháng', 'Giải quyết trợ cấp xã hội hằng tháng cho đối tượng bảo trợ xã hội đủ điều kiện.', 'Người đề nghị thuộc nhóm đối tượng bảo trợ xã hội và cư trú hợp pháp tại địa phương.', [['name' => 'assistance_group', 'label' => 'Nhóm đối tượng đề nghị trợ cấp', 'type' => 'text', 'required' => true]], [['code' => 'citizen_id_copy', 'label' => 'Bản sao căn cước công dân', 'required' => true, 'type' => 'pdf'], ['code' => 'eligibility_evidence', 'label' => 'Giấy tờ chứng minh hoàn cảnh', 'required' => true, 'type' => 'pdf']], 10, 0),
            $this->service('SOCIAL_WELFARE', 'LDAS', 'SOCIAL_FUNERAL_SUPPORT', 'Đề nghị hỗ trợ chi phí mai táng', 'Giải quyết hỗ trợ chi phí mai táng đối với đối tượng bảo trợ xã hội theo quy định.', 'Người đề nghị là cá nhân hoặc tổ chức trực tiếp thực hiện việc mai táng cho đối tượng đủ điều kiện.', [['name' => 'deceased_name', 'label' => 'Họ tên người đã mất', 'type' => 'text', 'required' => true], ['name' => 'date_of_death', 'label' => 'Ngày mất', 'type' => 'date', 'required' => true]], [['code' => 'death_certificate', 'label' => 'Trích lục khai tử', 'required' => true, 'type' => 'pdf'], ['code' => 'funeral_expense_evidence', 'label' => 'Giấy tờ xác nhận người tổ chức mai táng', 'required' => true, 'type' => 'pdf']], 5, 0),
            $this->service('MERITORIOUS_SERVICES', 'LDAS', 'MARTYR_RELATIVE_CONFIRMATION', 'Xác nhận thân nhân liệt sĩ', 'Xác nhận quan hệ thân nhân với liệt sĩ để thực hiện các chế độ ưu đãi người có công.', 'Người đề nghị có giấy tờ chứng minh quan hệ nhân thân và thông tin liệt sĩ trong hồ sơ quản lý.', [['name' => 'martyr_name', 'label' => 'Họ tên liệt sĩ', 'type' => 'text', 'required' => true], ['name' => 'relationship', 'label' => 'Quan hệ với liệt sĩ', 'type' => 'text', 'required' => true]], [['code' => 'relationship_document', 'label' => 'Giấy tờ chứng minh quan hệ thân nhân', 'required' => true, 'type' => 'pdf'], ['code' => 'martyr_record', 'label' => 'Thông tin hoặc hồ sơ liệt sĩ', 'required' => true, 'type' => 'pdf']], 10, 0),
            $this->service('MERITORIOUS_SERVICES', 'LDAS', 'MERITORIOUS_MONTHLY_ALLOWANCE', 'Đề nghị giải quyết trợ cấp ưu đãi hằng tháng', 'Tiếp nhận hồ sơ đề nghị hưởng trợ cấp ưu đãi hằng tháng đối với người có công hoặc thân nhân.', 'Người đề nghị thuộc diện hưởng chế độ ưu đãi và chưa được giải quyết trùng chế độ.', [['name' => 'beneficiary_group', 'label' => 'Diện đối tượng hưởng ưu đãi', 'type' => 'text', 'required' => true]], [['code' => 'meritorious_certificate', 'label' => 'Giấy tờ xác nhận người có công', 'required' => true, 'type' => 'pdf'], ['code' => 'bank_account_evidence', 'label' => 'Thông tin tài khoản nhận trợ cấp', 'required' => false, 'type' => 'pdf']], 15, 0),
            $this->service('URBAN_PLANNING', 'QLXD', 'PLANNING_INFORMATION_REQUEST', 'Cung cấp thông tin quy hoạch xây dựng', 'Cung cấp thông tin về chỉ giới, chức năng sử dụng đất và các chỉ tiêu quy hoạch tại địa điểm yêu cầu.', 'Người yêu cầu xác định được vị trí, ranh giới khu đất hoặc công trình cần tra cứu.', [['name' => 'planning_location', 'label' => 'Địa điểm cần cung cấp thông tin', 'type' => 'text', 'required' => true], ['name' => 'information_purpose', 'label' => 'Mục đích sử dụng thông tin', 'type' => 'text', 'required' => true]], [['code' => 'location_diagram', 'label' => 'Sơ đồ vị trí khu đất', 'required' => true, 'type' => 'pdf']], 7, 50000),
            $this->service('URBAN_PLANNING', 'QLXD', 'SITE_PLAN_APPROVAL', 'Chấp thuận phương án tổng mặt bằng', 'Thẩm định và chấp thuận phương án tổng mặt bằng của dự án theo quy hoạch được duyệt.', 'Chủ đầu tư có quyền sử dụng địa điểm hợp pháp và hồ sơ thiết kế phù hợp nhiệm vụ quy hoạch.', [['name' => 'project_name', 'label' => 'Tên dự án', 'type' => 'text', 'required' => true], ['name' => 'site_area', 'label' => 'Diện tích khu đất (m²)', 'type' => 'number', 'required' => true]], [['code' => 'site_plan', 'label' => 'Bản vẽ tổng mặt bằng', 'required' => true, 'type' => 'pdf'], ['code' => 'planning_basis', 'label' => 'Văn bản hoặc bản đồ quy hoạch làm căn cứ', 'required' => true, 'type' => 'pdf']], 15, 100000),
            $this->service('ENVIRONMENT', 'TNMT', 'ENVIRONMENTAL_REGISTRATION', 'Đăng ký môi trường', 'Tiếp nhận đăng ký môi trường của dự án đầu tư hoặc cơ sở sản xuất thuộc đối tượng phải đăng ký.', 'Chủ dự án hoặc cơ sở xác định đầy đủ nguồn phát sinh chất thải và biện pháp quản lý môi trường.', [['name' => 'facility_name', 'label' => 'Tên dự án hoặc cơ sở', 'type' => 'text', 'required' => true], ['name' => 'facility_address', 'label' => 'Địa điểm hoạt động', 'type' => 'text', 'required' => true]], [['code' => 'environmental_registration_form', 'label' => 'Bản đăng ký môi trường', 'required' => true, 'type' => 'pdf'], ['code' => 'facility_layout', 'label' => 'Sơ đồ mặt bằng dự án hoặc cơ sở', 'required' => true, 'type' => 'pdf']], 10, 0),
            $this->service('ENVIRONMENT', 'TNMT', 'ENVIRONMENTAL_PERMIT', 'Cấp giấy phép môi trường', 'Thẩm định và cấp giấy phép môi trường cho dự án, cơ sở có hoạt động xả thải thuộc thẩm quyền.', 'Dự án hoặc cơ sở đã hoàn thành công trình xử lý chất thải và đáp ứng yêu cầu bảo vệ môi trường.', [['name' => 'project_capacity', 'label' => 'Quy mô hoặc công suất hoạt động', 'type' => 'text', 'required' => true], ['name' => 'waste_source', 'label' => 'Nguồn phát sinh chất thải chính', 'type' => 'text', 'required' => true]], [['code' => 'permit_proposal_report', 'label' => 'Báo cáo đề xuất cấp giấy phép môi trường', 'required' => true, 'type' => 'pdf'], ['code' => 'environmental_legal_documents', 'label' => 'Hồ sơ pháp lý về môi trường đã được phê duyệt', 'required' => true, 'type' => 'pdf']], 30, 300000),
            $this->service('BUSINESS_REGISTRATION', 'TCKH', 'HOUSEHOLD_BUSINESS_REGISTRATION', 'Đăng ký thành lập hộ kinh doanh', 'Cấp giấy chứng nhận đăng ký hộ kinh doanh cho cá nhân hoặc các thành viên hộ gia đình.', 'Ngành nghề đăng ký không thuộc danh mục cấm và tên hộ kinh doanh đáp ứng quy định.', [['name' => 'business_name', 'label' => 'Tên hộ kinh doanh dự kiến', 'type' => 'text', 'required' => true], ['name' => 'business_address', 'label' => 'Địa điểm kinh doanh', 'type' => 'text', 'required' => true], ['name' => 'business_capital', 'label' => 'Vốn kinh doanh', 'type' => 'number', 'required' => true]], [['code' => 'business_registration_request', 'label' => 'Giấy đề nghị đăng ký hộ kinh doanh', 'required' => true, 'type' => 'pdf'], ['code' => 'member_agreement', 'label' => 'Biên bản họp thành viên hộ gia đình', 'required' => false, 'type' => 'pdf']], 3, 100000),
            $this->service('BUSINESS_REGISTRATION', 'TCKH', 'HOUSEHOLD_BUSINESS_CHANGE', 'Đăng ký thay đổi nội dung hộ kinh doanh', 'Cập nhật tên, địa chỉ, ngành nghề, vốn hoặc thông tin chủ hộ trên giấy chứng nhận đăng ký hộ kinh doanh.', 'Hộ kinh doanh đang hoạt động hợp pháp và nội dung thay đổi không vi phạm quy định đăng ký kinh doanh.', [['name' => 'business_code', 'label' => 'Mã số hộ kinh doanh', 'type' => 'text', 'required' => true], ['name' => 'change_content', 'label' => 'Nội dung đề nghị thay đổi', 'type' => 'text', 'required' => true]], [['code' => 'change_notification', 'label' => 'Thông báo thay đổi nội dung đăng ký', 'required' => true, 'type' => 'pdf'], ['code' => 'current_certificate', 'label' => 'Giấy chứng nhận đăng ký hộ kinh doanh hiện tại', 'required' => true, 'type' => 'pdf']], 3, 50000),
            $this->service('INVESTMENT_SUPPORT', 'TCKH', 'INVESTMENT_PROJECT_REGISTRATION', 'Đăng ký dự án đầu tư trong nước', 'Tiếp nhận thông tin và hồ sơ đăng ký dự án đầu tư thuộc thẩm quyền giải quyết của địa phương.', 'Nhà đầu tư chứng minh tư cách pháp lý, năng lực tài chính và đề xuất dự án phù hợp quy hoạch.', [['name' => 'project_name', 'label' => 'Tên dự án đầu tư', 'type' => 'text', 'required' => true], ['name' => 'investment_capital', 'label' => 'Tổng vốn đầu tư', 'type' => 'number', 'required' => true], ['name' => 'project_location', 'label' => 'Địa điểm thực hiện dự án', 'type' => 'text', 'required' => true]], [['code' => 'investment_proposal', 'label' => 'Văn bản đề nghị thực hiện dự án đầu tư', 'required' => true, 'type' => 'pdf'], ['code' => 'financial_capacity', 'label' => 'Tài liệu chứng minh năng lực tài chính', 'required' => true, 'type' => 'pdf']], 15, 0),
            $this->service('INVESTMENT_SUPPORT', 'TCKH', 'SME_SUPPORT_REGISTRATION', 'Đăng ký tham gia chương trình hỗ trợ doanh nghiệp nhỏ và vừa', 'Tiếp nhận nhu cầu tư vấn, đào tạo, chuyển đổi số hoặc hỗ trợ phát triển thị trường của doanh nghiệp nhỏ và vừa.', 'Doanh nghiệp đáp ứng tiêu chí doanh nghiệp nhỏ và vừa, đang hoạt động và không có vi phạm nghiêm trọng.', [['name' => 'enterprise_name', 'label' => 'Tên doanh nghiệp', 'type' => 'text', 'required' => true], ['name' => 'support_program', 'label' => 'Nội dung hỗ trợ đề nghị', 'type' => 'text', 'required' => true]], [['code' => 'enterprise_registration', 'label' => 'Giấy chứng nhận đăng ký doanh nghiệp', 'required' => true, 'type' => 'pdf'], ['code' => 'support_proposal', 'label' => 'Bản đề xuất nhu cầu hỗ trợ', 'required' => true, 'type' => 'pdf']], 10, 0),
            $this->service('CULTURE_SPORTS_TOURISM', 'VHTT', 'PUBLIC_PERFORMANCE_LICENSE', 'Cấp giấy phép tổ chức biểu diễn nghệ thuật', 'Thẩm định nội dung, địa điểm và cấp phép tổ chức chương trình biểu diễn nghệ thuật phục vụ công chúng.', 'Tổ chức, cá nhân có tư cách pháp lý và nội dung biểu diễn phù hợp quy định về văn hóa.', [['name' => 'program_name', 'label' => 'Tên chương trình biểu diễn', 'type' => 'text', 'required' => true], ['name' => 'performance_date', 'label' => 'Ngày dự kiến biểu diễn', 'type' => 'date', 'required' => true], ['name' => 'venue', 'label' => 'Địa điểm tổ chức', 'type' => 'text', 'required' => true]], [['code' => 'program_script', 'label' => 'Kịch bản hoặc danh mục nội dung biểu diễn', 'required' => true, 'type' => 'pdf'], ['code' => 'venue_agreement', 'label' => 'Văn bản đồng ý của đơn vị quản lý địa điểm', 'required' => true, 'type' => 'pdf']], 7, 200000),
            $this->service('CULTURE_SPORTS_TOURISM', 'VHTT', 'SPORTS_BUSINESS_CERTIFICATE', 'Cấp giấy chứng nhận đủ điều kiện kinh doanh hoạt động thể thao', 'Thẩm định cơ sở vật chất, trang thiết bị và nhân sự chuyên môn của cơ sở kinh doanh thể thao.', 'Cơ sở đáp ứng điều kiện chuyên môn, an toàn và cơ sở vật chất theo môn thể thao đăng ký.', [['name' => 'sports_type', 'label' => 'Môn thể thao kinh doanh', 'type' => 'text', 'required' => true], ['name' => 'facility_address', 'label' => 'Địa chỉ cơ sở', 'type' => 'text', 'required' => true]], [['code' => 'business_registration', 'label' => 'Giấy chứng nhận đăng ký doanh nghiệp hoặc hộ kinh doanh', 'required' => true, 'type' => 'pdf'], ['code' => 'staff_qualifications', 'label' => 'Văn bằng chuyên môn của nhân viên hướng dẫn', 'required' => true, 'type' => 'pdf']], 10, 500000),
            $this->service('INFORMATION_COMMUNICATIONS', 'VHTT', 'NON_COMMERCIAL_PUBLICATION_LICENSE', 'Cấp giấy phép xuất bản tài liệu không kinh doanh', 'Cấp phép xuất bản tài liệu tuyên truyền, hướng dẫn nghiệp vụ hoặc phục vụ nhiệm vụ chính trị không nhằm mục đích kinh doanh.', 'Cơ quan, tổ chức chịu trách nhiệm về nội dung và tài liệu không thuộc danh mục cấm phổ biến.', [['name' => 'publication_title', 'label' => 'Tên tài liệu xuất bản', 'type' => 'text', 'required' => true], ['name' => 'print_quantity', 'label' => 'Số lượng bản in', 'type' => 'number', 'required' => true]], [['code' => 'publication_manuscript', 'label' => 'Bản thảo hoàn chỉnh của tài liệu', 'required' => true, 'type' => 'pdf'], ['code' => 'organization_request', 'label' => 'Văn bản đề nghị của cơ quan, tổ chức', 'required' => true, 'type' => 'pdf']], 10, 100000),
            $this->service('INFORMATION_COMMUNICATIONS', 'VHTT', 'GENERAL_WEBSITE_LICENSE', 'Cấp giấy phép thiết lập trang thông tin điện tử tổng hợp', 'Thẩm định và cấp phép thiết lập trang thông tin điện tử tổng hợp cung cấp thông tin trên mạng.', 'Tổ chức có chức năng phù hợp, nhân sự quản lý nội dung và phương án kỹ thuật bảo đảm an toàn thông tin.', [['name' => 'website_name', 'label' => 'Tên trang thông tin điện tử', 'type' => 'text', 'required' => true], ['name' => 'domain_name', 'label' => 'Tên miền sử dụng', 'type' => 'text', 'required' => true]], [['code' => 'website_operation_plan', 'label' => 'Đề án hoạt động của trang thông tin điện tử', 'required' => true, 'type' => 'pdf'], ['code' => 'domain_evidence', 'label' => 'Tài liệu chứng minh quyền sử dụng tên miền', 'required' => true, 'type' => 'pdf']], 15, 200000),
            $this->service('EDUCATION_SUPPORT', 'GDDT', 'TUITION_EXEMPTION_REDUCTION', 'Đề nghị miễn, giảm học phí', 'Giải quyết miễn hoặc giảm học phí cho người học thuộc nhóm đối tượng được hưởng chính sách.', 'Người học đang theo học tại cơ sở giáo dục và có giấy tờ chứng minh thuộc diện miễn, giảm học phí.', [['name' => 'student_name', 'label' => 'Họ và tên người học', 'type' => 'text', 'required' => true], ['name' => 'school_name', 'label' => 'Tên cơ sở giáo dục', 'type' => 'text', 'required' => true]], [['code' => 'student_confirmation', 'label' => 'Giấy xác nhận đang theo học', 'required' => true, 'type' => 'pdf'], ['code' => 'policy_eligibility', 'label' => 'Giấy tờ chứng minh đối tượng chính sách', 'required' => true, 'type' => 'pdf']], 10, 0),
            $this->service('EDUCATION_SUPPORT', 'GDDT', 'LEARNING_COST_SUPPORT', 'Đề nghị hỗ trợ chi phí học tập', 'Giải quyết hỗ trợ chi phí học tập cho trẻ em, học sinh thuộc đối tượng theo quy định.', 'Người học có hoàn cảnh và điều kiện đáp ứng chính sách hỗ trợ chi phí học tập hiện hành.', [['name' => 'student_name', 'label' => 'Họ và tên học sinh', 'type' => 'text', 'required' => true], ['name' => 'academic_year', 'label' => 'Năm học đề nghị hỗ trợ', 'type' => 'text', 'required' => true]], [['code' => 'school_confirmation', 'label' => 'Xác nhận của cơ sở giáo dục', 'required' => true, 'type' => 'pdf'], ['code' => 'household_evidence', 'label' => 'Giấy tờ chứng minh hoàn cảnh hộ gia đình', 'required' => true, 'type' => 'pdf']], 10, 0),
        ];
    }

    /**
     * @param  Collection<string, User>  $citizens
     * @param  Collection<string, ServiceType>  $services
     * @param  Collection<string, Collection<int, User>>  $staffByDepartment
     */
    private function seedApplications(Collection $citizens, Collection $services, Collection $staffByDepartment): void
    {
        $this->createApplication('HS-20260820-000001', $citizens['citizen1@example.test'], $services['CIVIL_STATUS_CERTIFICATE'], ApplicationStatus::Received, 2);
        $this->createApplication('HS-20260820-000002', $citizens['citizen1@example.test'], $services['CONSTRUCTION_PERMIT'], ApplicationStatus::Processing, 3, $staffByDepartment['QLXD']->first());
        $this->createApplication('HS-20260820-000003', $citizens['citizen2@example.test'], $services['PUBLIC_SCHOOL_ENROLLMENT'], ApplicationStatus::SupplementRequired, 4, $staffByDepartment['GDDT']->first());
        $this->createApplication('HS-20260820-000004', $citizens['citizen2@example.test'], $services['HEALTHCARE_SUPPORT_REGISTRATION'], ApplicationStatus::Approved, 6, $staffByDepartment['YTE']->first());
        $this->createApplication('HS-20260820-000005', $citizens['citizen1@example.test'], $services['LAND_RECORD_INFORMATION'], ApplicationStatus::Rejected, 5, $staffByDepartment['TNMT']->first());
    }

    private function createApplication(string $code, User $citizen, ServiceType $service, ApplicationStatus $status, int $daysAgo, ?User $assignedStaff = null): void
    {
        $attributes = [
            'application_code' => $code,
            'citizen_id' => $citizen->id,
            'service_type_id' => $service->id,
            'assigned_staff_id' => $assignedStaff?->id,
            'status' => $status,
            'form_data' => ['full_name' => $citizen->name],
            'submitted_at' => now()->subDays($daysAgo),
            'processing_started_at' => null,
            'completed_at' => null,
            'result_note' => null,
            'rejection_reason' => null,
        ];

        if (in_array($status, [ApplicationStatus::Processing, ApplicationStatus::SupplementRequired, ApplicationStatus::Approved, ApplicationStatus::Rejected], true)) {
            $attributes['processing_started_at'] = now()->subDays(max(1, $daysAgo - 1));
        }
        if ($status === ApplicationStatus::Approved) {
            $attributes['completed_at'] = now()->subDay();
            $attributes['result_note'] = 'Đã giải quyết hồ sơ và cấp kết quả theo quy định.';
        }
        if ($status === ApplicationStatus::Rejected) {
            $attributes['completed_at'] = now()->subDay();
            $attributes['rejection_reason'] = 'Hồ sơ chưa đáp ứng đầy đủ điều kiện giải quyết.';
        }

        $application = Application::query()->create($attributes);
        $application->statusHistories()->create(['from_status' => null, 'to_status' => ApplicationStatus::Received, 'changed_by' => $citizen->id, 'note' => 'Hồ sơ được nộp trực tuyến.']);

        if (in_array($status, [ApplicationStatus::Processing, ApplicationStatus::SupplementRequired, ApplicationStatus::Approved, ApplicationStatus::Rejected], true)) {
            $application->statusHistories()->create(['from_status' => ApplicationStatus::Received, 'to_status' => ApplicationStatus::Processing, 'changed_by' => $assignedStaff?->id ?? $citizen->id, 'note' => 'Cán bộ bắt đầu xử lý hồ sơ.']);
        }
        if ($status === ApplicationStatus::SupplementRequired) {
            $application->statusHistories()->create(['from_status' => ApplicationStatus::Processing, 'to_status' => ApplicationStatus::SupplementRequired, 'changed_by' => $assignedStaff?->id ?? $citizen->id, 'note' => 'Cần bổ sung giấy tờ chứng minh nơi cư trú.']);
        }
        if ($status === ApplicationStatus::Approved) {
            $application->statusHistories()->create(['from_status' => ApplicationStatus::Processing, 'to_status' => ApplicationStatus::Approved, 'changed_by' => $assignedStaff?->id ?? $citizen->id, 'note' => 'Hồ sơ đã được kiểm tra đầy đủ và phê duyệt.']);
        }
        if ($status === ApplicationStatus::Rejected) {
            $application->statusHistories()->create(['from_status' => ApplicationStatus::Processing, 'to_status' => ApplicationStatus::Rejected, 'changed_by' => $assignedStaff?->id ?? $citizen->id, 'note' => $attributes['rejection_reason']]);
        }
        if ($assignedStaff !== null) {
            $application->assignments()->create([
                'staff_id' => $assignedStaff->id,
                'department_id' => $service->responsible_department_id,
                'assigned_by' => $service->responsibleDepartment->leader_id,
                'assigned_at' => now()->subDays(max(1, $daysAgo - 1)),
                'ended_at' => null,
            ]);
        }
    }

    /** @param array<string, mixed> $attributes */
    /** @param array<string, mixed> $attributes */
    private function createUser(UserRole $role, array $attributes): User
    {
        return User::query()->create([
            ...$attributes,
            'password' => 'password',
            'role' => $role,
            'email_notifications_enabled' => $role === UserRole::Citizen,
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $formSchema
     * @param  array<int, array<string, mixed>>  $documentRequirements
     * @return array<string, mixed>
     */
    private function service(string $categoryCode, string $departmentCode, string $code, string $name, string $description, string $requirements, array $formSchema, array $documentRequirements, int $processingTimeDays, int $fee): array
    {
        return [
            'category_code' => $categoryCode,
            'department_code' => $departmentCode,
            'code' => $code,
            'name' => $name,
            'description' => $description,
            'requirements' => $requirements,
            'form_schema' => $formSchema,
            'document_requirements' => $documentRequirements,
            'processing_time_days' => $processingTimeDays,
            'fee' => $fee,
        ];
    }
}
