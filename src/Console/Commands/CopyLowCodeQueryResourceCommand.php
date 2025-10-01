<?php
namespace BrightLiu\LowCode\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyLowCodeQueryResourceCommand extends Command
{
    protected $signature = 'lowcode:install-list-query-resource {--f : 覆盖已存在的文件}';

    protected $description = '安装列表数据源';

    public function handle()
    {
        $className = 'QuerySource';
        $dir = "Http/Resources/LowCode/LowCodeList";

        // 构建正确的目标路径
        $targetDir = app_path($dir);
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $className . '.php';

        // 确保目录存在
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
            $this->info("已创建目录: {$targetDir}");
        }

        // 检查文件是否已存在
        if (File::exists($targetPath) && !$this->option('f')) {
            $this->error("{$dir} {$className} 已经存在，请勿重复安装。! 使用 --f 选项覆盖。");
            return;
        }

        // 文件内容
        $content = <<<'EOT'
<?php

declare(strict_types = 1);

namespace App\Http\Resources\LowCode\LowCodeList;

use BrightLiu\LowCode\Tools\Mask;
use Illuminate\Http\Request;
use App\Models\LowCode\LowCodeList;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LowCodeList
 */
final class QuerySource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        // 以下字段是数据库字段，请根据实际需求修改
        return [
            "ptt_nm"               => $this->ptt_nm ?? '',
            "id_crd_no"            => $this->id_crd_no ?? '',
            "masked_id_crd_no"     => Mask::idcard($this->id_crd_no ?? ''),
            "masked_slf_tel_no"    => Mask::phone($this->slf_tel_no ?? ''),
            "user_id"              => $this->user_id ?? '',
            "bth_dt"               => $this->bth_dt ?? null,
            "age"                  => $this->age ?? 0,
            "slf_tel_no"           => $this->slf_tel_no ?? '',
            "gdr_cd"               => $this->gdr_cd ?? 9,
            "gdr_nm"               => $this->gdr_nm ?? '',
            "bld_cd"               => $this->bld_cd ?? '',
            "bld_nm"               => $this->bld_nm ?? '',
            "fml_chrnc_dses_hst"   => $this->fml_chrnc_dses_hst ?? '',
            "fml_hst"              => $this->fml_hst ?? '',
            "smk_hst"              => $this->smk_hst ?? '',
            "past_hst"             => $this->past_hst ?? '',
            "algn_src"             => $this->algn_src ?? '',
            "liv_hbt"              => $this->liv_hbt ?? '',
            "hgt"                  => $this->hgt ?? 0.00,
            "wgt"                  => $this->wgt ?? 0.00,
            "bmi"                  => $this->bmi ?? '',
            "curr_addr"            => $this->curr_addr ?? '',
            "curr_addr_prv_cd"     => $this->curr_addr_prv_cd ?? '',
            "curr_addr_prv_nm"     => $this->curr_addr_prv_nm ?? '',
            "curr_addr_cty_cd"     => $this->curr_addr_cty_cd ?? '',
            "curr_addr_cty_nm"     => $this->curr_addr_cty_nm ?? '',
            "curr_addr_cnty_cd"    => $this->curr_addr_cnty_cd ?? "",
            "curr_addr_cnty_nm"    => $this->curr_addr_cnty_nm ?? '',
            "curr_addr_twn_cd"     => $this->curr_addr_twn_cd ?? '',
            "curr_addr_twn_nm"     => $this->curr_addr_twn_nm ?? '',
            "curr_addr_vlg_cd"     => $this->curr_addr_vlg_cd ?? "",
            "curr_addr_vlg_nm"     => $this->curr_addr_vlg_nm ?? "",
            "pblc_hlth_flg"        => $this->pblc_hlth_flg ?? '',
            "dth_flg"              => $this->dth_flg ?? "",
            "dth_flg_nm"           => match ($this->dth_flg) {
                0       => '否',
                1       => '是',
                default => '--',
            },
            "dth_dt"          => $this->dth_dt ?? null,
            "psn_adtn_flg"    => $this->psn_adtn_flg ?? 0,
            "psn_adtn_flg_nm" => match ($this->psn_adtn_flg) {
                0       => '否',
                1       => '是',
                default => '--',
            },
            "psn_adtn_tm"         => $this->psn_adtn_tm ?? null,
            "spcl_crt_rcd_flg"    => $this->spcl_crt_rcd_flg ?? 0,
            "spcl_crt_rcd_flg_nm" => match ($this->spcl_crt_rcd_flg) {
                0       => '否',
                1       => '是',
                default => '--',
            },
            "rcd_mng_cmnt_cd"       => $this->rcd_mng_cmnt_cd ?? '',
            "rcd_mng_cmnt_nm"       => $this->rcd_mng_cmnt_nm ?? '',
            "rcd_mng_org_cd"        => $this->rcd_mng_org_cd ?? '',
            "rcd_mng_org_nm"        => $this->rcd_mng_org_nm ?? '',
            "rcd_mng_team_cd"       => $this->rcd_mng_team_cd ?? '',
            "rcd_mng_team_nm"       => $this->rcd_mng_team_nm ?? '',
            "rspsblt_dct_cd"        => $this->rspsblt_dct_cd ?? '',
            "rspsblt_dct_nm"        => $this->rspsblt_dct_nm ?? '',
            "rspsblt_dct_id_crd_no" => $this->rspsblt_dct_id_crd_no ?? '',
            "rspsblt_dct_tel_no"    => $this->rspsblt_dct_tel_no ?? '',
            "sgn_ctrct_stts_cd"     => $this->sgn_ctrct_stts_cd ?? '',
            "sgn_ctrct_stts_cd_nm" => match ($this->sgn_ctrct_stts_cd) {
                3       => '正式签约',
                5       => '到期',
                7       => '未签约',
                default => '--',
            },
            "sgn_ctrct_stts_nm"      => $this->sgn_ctrct_stts_nm ?? '',
            "sgn_ctrct_dt"           => $this->sgn_ctrct_dt ?? null,
            "sgn_ctrct_org_cd"       => $this->sgn_ctrct_org_cd ?? '',
            "sgn_ctrct_org_nm"       => $this->sgn_ctrct_org_nm ?? '',
            "sgn_ctrct_dct_cd"       => $this->sgn_ctrct_dct_cd ?? '',
            "sgn_ctrct_dct_nm"       => $this->sgn_ctrct_dct_nm ?? '',
            "sgst_sgn_ctrct_team_cd" => $this->sgst_sgn_ctrct_team_cd ?? '',
            "sgst_sgn_ctrct_team_nm" => $this->sgst_sgn_ctrct_team_nm ?? '',
            "spcl_dses_dct_nm"       => $this->spcl_dses_dct_nm ?? '',
            "cfm_diag_flg"           => $this->cfm_diag_flg ?? -1,
            "cfm_diag_flg_nm"        => match ($this->cfm_diag_flg) {
                1       => '是',
                0       => '否',
                9       => '其他',
                default => '--',
            },
            "cfm_diag_dt"            => $this->cfm_diag_dt ?? null,
            "spcl_dses_year_vst_tm"  => $this->spcl_dses_year_vst_tm ?? null,
            "last_spcl_dses_vst_tm"  => $this->last_spcl_dses_vst_tm ?? null,
            "rmrk"                   => $this->rmrk ?? '',
            "rsk_elmt"               => $this->rsk_elmt ?? '',
            "ptt_crwd_clsf_cd"       => $this->ptt_crwd_clsf_cd ?? '',
            "ptt_crwd_clsf_nm"       => $this->ptt_crwd_clsf_nm ?? '',
            "ptt_crwd_clsf_tm"       => $this->ptt_crwd_clsf_tm ?? null,
            "incprt_into_mng_flg"    => $this->incprt_into_mng_flg ?? -1,
            "incprt_into_mng_flg_nm" => match ($this->incprt_into_mng_flg) {
                1       => '已纳管',
                0       => '待纳管',
                2       => '推荐纳管',
                9       => '出组',
                default => '--',
            },
            "incprt_into_mng_tm" => $this->incprt_into_mng_tm ?? '',
            "out_grp_tm"         => $this->out_grp_tm ?? null,
            "out_grp_cas"        => $this->out_grp_cas ?? '',
            "infmd_agr_rcd_stts" => $this->infmd_agr_rcd_stts ?? "",
            "infmd_agr_rcd_stts_nm" => match ($this->infmd_agr_rcd_stts) {
                1       => '已签署',
                0       => '未签署',
                9       => '其他',
                default => '--',
            },
            "incprt_into_mng_org_cd"      => $this->incprt_into_mng_org_cd ?? '',
            "incprt_into_mng_org_nm"      => $this->incprt_into_mng_org_nm ?? '',
            "incprt_into_mng_dct_cd"      => $this->incprt_into_mng_dct_cd ?? '',
            "incprt_into_mng_dct_nm"      => $this->incprt_into_mng_dct_nm ?? '',
            "incprt_into_mng_dct_team_cd" => $this->incprt_into_mng_dct_team_cd ?? '',
            "incprt_into_mng_dct_team_nm" => $this->incprt_into_mng_dct_team_nm ?? '',
            "fst_vst_tm"                  => $this->fst_vst_tm ?? null,
            "sbsqt_vst_tm"                => $this->sbsqt_vst_tm ?? null,
            "next_rvw_tm"                 => $this->next_rvw_tm ?? null,
            "rvw_flg"                     => $this->rvw_flg ?? 9,
            "rvw_flg_nm"                  => match ($this->rvw_flg) {
                1       => '是',
                0       => '否',
                9       => '其他',
                default => '--',
            },
            "on_tm_rvw_flg" => $this->on_tm_rvw_flg ?? 9,
            "on_tm_rvw_flg_nm" => match ($this->on_tm_rvw_flg) {
                1       => '是',
                0       => '否',
                9       => '未知',
                default => '--',
            },
            "cplct_dses_nms" => $this->cplct_dses_nms ?? '',
            "upd_dt"         => $this->upd_dt ?? null,
            "crt_tm"         => $this->crt_tm ?? null,
            "invld_flg"      => $this->invld_flg ?? 0,
            "invld_flg_nm" => match ($this->invld_flg) {
                1       => '是',
                0       => '否',
                default => '--',
            },
            "incprt_into_mng_src"    => $this->incprt_into_mng_src ?? -1,
            "incprt_into_mng_src_nm" => match ($this->incprt_into_mng_src) {
                1       => '手动纳管',
                2       => '推荐纳管',
                3       => '知情同意书签署',
                4       => '企业微信',
                default => '--',
            },
        ];
    }
}
EOT;

        // 写入文件
        File::put($targetPath, $content);

        $this->info("安装成功: {$className}");
    }
}