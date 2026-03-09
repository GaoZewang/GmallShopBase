# Gmall 电商平台

Gmall 是一个基于 Webman 框架构建的高性能电商平台系统，集成了商品管理、订单处理、支付功能等电商核心模块。

## 项目特性

- **高性能**：基于 Workerman 的异步 HTTP 服务框架
- **现代化架构**：采用 PHP 8.1+，支持最新语言特性
- **完整电商功能**：包含商品、订单、支付、用户管理等模块
- **多端支持**：支持 WEB、H5、APP 等多种终端
- **安全可靠**：集成 JWT 认证，保障系统安全
- **支付集成**：支持支付宝、微信等多种支付方式

## 技术栈

- **框架**：[Webman](https://www.workerman.net) (基于 Workerman)
- **语言**：PHP 8.1+
- **数据库**：MySQL
- **缓存**：Redis
- **认证**：JWT
- **支付**：Yansongda Pay
- **前端**：Vue 3 + TypeScript (console/gmall)

## 功能模块

### 后台管理
- 管理员用户管理
- 商品管理（分类、商品信息、库存等）
- 商家管理
- 店铺管理
- 系统设置（权限、角色、配置）

### API 接口
- 用户注册/登录
- 商品浏览/搜索
- 订单创建/管理
- 支付接口

### 支付系统
- 支付宝支付（WEB、H5、APP）
- 微信支付
- 统一支付接口

## 系统架构

```
├── app                 # 应用目录
│   ├── admin          # 后台管理模块
│   ├── api            # API 接口模块
│   ├── service        # 服务层
│   │   └── pay        # 支付服务
│   └── model          # 数据模型
├── console/gmall      # 前端管理界面 (Vue3 + TypeScript)
├── config            # 配置文件
├── public            # 静态资源
└── support           # 框架支持文件
```

## 安装部署

### 环境要求
- PHP >= 8.1
- MySQL
- Redis
- Composer

### 安装步骤

1. 克隆项目
```bash
git clone <your-repo-url>
cd Gmall
```

2. 安装依赖
```bash
composer install
```

3. 配置环境
```bash
cp .env.example .env
# 编辑 .env 文件配置数据库、Redis 等信息
```

4. 初始化数据库
```bash
# 导入数据库结构
mysql -u username -p database_name < public/gmall_shop.sql
```

5. 启动服务
```bash
php start.php start
```

## 前端管理界面

管理后台位于 [console/gmall](file:///G:/WWW/Gmall/console/gmall) 目录，使用 Vue 3 + TypeScript + Vite 构建。

### 前端开发
```bash
cd console/gmall
npm install
npm run dev
```

### 前端生产构建
```bash
npm run build
```

## 支付配置

系统集成了支付宝和微信支付，需要在配置文件中设置相应的密钥信息：

1. 在 [config/payment.php](file:///G:/WWW/Gmall/config/payment.php) 中配置支付参数
2. 根据不同支付场景（WEB、H5、APP）进行相应配置

## API 接口

系统提供完整的 RESTful API 接口，支持：
- 用户认证
- 商品管理
- 订单处理
- 支付回调

## 开发规范

- 遵循 PSR-4 自动加载规范
- 使用命名空间组织代码
- 统一的代码风格
- 完善的注释说明

## 许可证

本项目基于 MIT 许可证开源。

## 贡献

欢迎提交 Issue 和 Pull Request 来改进项目。