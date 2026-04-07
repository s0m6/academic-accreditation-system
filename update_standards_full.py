import re

def generate_indicator(std_num, ind_num, text, tooltip=""):
    key = f"{std_num}-{ind_num}"
    tooltip_html = f"""
                                <div class="tooltip-container mr-2">
                                    <svg class="w-5 h-5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="tooltip-text">{tooltip}</div>
                                </div>""" if tooltip else ""
    
    return f"""
                        <!-- Indicator {key} -->
                        <div class="indicator-row rounded-xl p-5 border border-slate-600 bg-slate-800/50" data-indicator="{key}">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر {key}</span>
                                    <p class="text-white mt-1">{text}</p>
                                </div>{tooltip_html}
                            </div>
                            <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                                <div class="flex gap-2">
                                    <button onclick="setRating({std_num}, {ind_num}, 1)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500" data-rating="1">1</button>
                                    <button onclick="setRating({std_num}, {ind_num}, 2)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500" data-rating="2">2</button>
                                    <button onclick="setRating({std_num}, {ind_num}, 3)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500" data-rating="3">3</button>
                                    <button onclick="setRating({std_num}, {ind_num}, 4)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500" data-rating="4">4</button>
                                    <button onclick="setRating({std_num}, {ind_num}, 5)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500" data-rating="5">5</button>
                                </div>
                            </div>
                            <div id="evidences-{key}" class="space-y-2 mb-3"></div>
                            <button onclick="addEvidence({std_num}, {ind_num})" class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> إرفاق دليل
                            </button>
                        </div>"""

def generate_substandard(std_num, sub_num, title, indicators):
    ind_html = "\n".join([generate_indicator(std_num, i["num"], i["text"], i.get("tooltip", "")) for i in indicators])
    return f"""
                <!-- Substandard {std_num}.{sub_num} -->
                <div class="bg-slate-800/80 rounded-2xl shadow-lg border border-slate-700/50 overflow-hidden">
                    <div class="bg-slate-700/30 p-4 border-b border-slate-700/50 flex items-center gap-3">
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg font-bold text-sm">{std_num}.{sub_num}</span>
                        <h3 class="font-bold text-lg text-white">{title}</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        {ind_html}
                    </div>
                </div>"""

def generate_standard(num, title, substandards, indicators_count):
    sub_html = "\n".join([generate_substandard(num, i+1, sub["title"], sub["indicators"]) for i, sub in enumerate(substandards)])
    
    return f"""
          <!-- Standard {num} -->
          <div id="standard-{num}-tab-content" class="std-content {'hidden' if num > 1 else ''} fade-in">
            <!-- Standard Header -->
            <div class="bg-slate-800 rounded-2xl p-6 shadow-xl mb-8 border-r-4 border-emerald-500">
              <div class="flex items-center justify-between">
                <div>
                  <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400 font-bold text-xl">{num}</div>
                    المعيار {'الأول' if num==1 else 'الثاني' if num==2 else 'الثالث' if num==3 else 'الرابع' if num==4 else 'الخامس' if num==5 else 'السادس' if num==6 else 'السابع'}: {title}
                  </h2>
                  <p class="text-slate-400 text-sm mt-2">يحتوي على {indicators_count} مؤشرات موزعة على المعايير الفرعية</p>
                </div>
                <div class="text-left bg-slate-900/50 p-4 rounded-xl border border-slate-700">
                  <span class="block text-xs text-slate-400 mb-1 text-center">التقييم</span>
                  <div class="flex items-center justify-center gap-1">
                      <span id="standard-{num}-score" class="text-3xl font-bold text-emerald-400">0.0</span>
                      <span class="text-slate-500 text-lg">/5</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Substandards Container -->
            <div class="space-y-8">
                {sub_html}
            </div>
            
            <!-- Standard Comments -->
            <div class="mt-8 bg-slate-800 rounded-2xl shadow-xl overflow-hidden border border-slate-700">
                <div class="bg-slate-700/50 p-4 border-b border-slate-600">
                    <h4 class="font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg> التعليقات الختامية للمعيار
                    </h4>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">تعليق البرنامج</label> 
                        <textarea rows="3" placeholder="أدخل تعليق البرنامج على هذا المعيار..." class="w-full px-4 py-3 rounded-xl resize-none bg-slate-900 border border-slate-600 focus:ring-2 focus:ring-blue-500 text-white" onchange="saveStandardComment({num}, 'program_comment', this.value)"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-emerald-400 mb-2">جوانب القوة</label> 
                        <textarea rows="3" placeholder="• نقطة قوة 1" class="w-full px-4 py-3 rounded-xl resize-none bg-emerald-900/10 border border-emerald-800/50 focus:ring-2 focus:ring-emerald-500 text-white" onchange="saveStandardComment({num}, 'strengths', this.value)"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-red-400 mb-2">جوانب تحتاج تحسين</label> 
                        <textarea rows="3" placeholder="• جانب للتحسين 1" class="w-full px-4 py-3 rounded-xl resize-none bg-red-900/10 border border-red-800/50 focus:ring-2 focus:ring-red-500 text-white" onchange="saveStandardComment({num}, 'improvements', this.value)"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-amber-400 mb-2">أولويات التحسين</label> 
                        <textarea rows="3" placeholder="• أولوية 1" class="w-full px-4 py-3 rounded-xl resize-none bg-amber-900/10 border border-amber-800/50 focus:ring-2 focus:ring-amber-500 text-white" onchange="saveStandardComment({num}, 'priorities', this.value)"></textarea>
                    </div>
                    <div class="pt-4 border-t border-slate-700">
                        <label class="block text-sm font-medium text-blue-400 mb-2">الرأي المستقل</label> 
                        <textarea rows="3" placeholder="أدخل الرأي المستقل..." class="w-full px-4 py-3 rounded-xl resize-none bg-blue-900/10 border border-blue-800/50 focus:ring-2 focus:ring-blue-500 text-white" onchange="saveStandardComment({num}, 'independent_opinion', this.value)"></textarea>
                    </div>
                </div>
            </div>
          </div>"""

standards_data = [
    {
        "num": 1,
        "title": "الرسالة والأهداف",
        "indicators_count": 5,
        "substandards": [
            {"title": "رسالة البرنامج", "indicators": [
                {"num": 1, "text": "وضوح رسالة البرنامج واتساقها مع رسالة المؤسسة", "tooltip": "<strong>أمثلة للأدلة:</strong><br>• وثيقة الرسالة المعتمدة<br>• محاضر اجتماعات المراجعة<br>• استبيانات أصحاب المصلحة"},
                {"num": 2, "text": "مشاركة أصحاب المصلحة في صياغة الرسالة والأهداف", "tooltip": "<strong>أمثلة للأدلة:</strong><br>• محاضر الاجتماعات<br>• قوائم الحضور<br>• نماذج الاستبيانات"}
            ]},
            {"title": "أهداف البرنامج", "indicators": [
                {"num": 3, "text": "قابلية قياس أهداف البرنامج"},
                {"num": 4, "text": "توافق أهداف البرنامج مع رسالته"},
                {"num": 5, "text": "آلية المراجعة الدورية للرسالة والأهداف"}
            ]}
        ]
    },
    {
        "num": 2,
        "title": "إدارة البرنامج وضمان الجودة",
        "indicators_count": 6,
        "substandards": [
            {"title": "إدارة البرنامج", "indicators": [
                {"num": 1, "text": "فاعلية الهيكل التنظيمي للبرنامج"},
                {"num": 2, "text": "وضوح المهام والمسؤوليات للقيادات واللجان"},
                {"num": 3, "text": "كفاءة نظام إدارة المعلومات والسجلات"}
            ]},
            {"title": "ضمان جودة البرنامج", "indicators": [
                {"num": 4, "text": "مشاركة جميع المنسوبين في عمليات الجودة"},
                {"num": 5, "text": "فاعلية نظام التقويم والمراجعة الداخلية"},
                {"num": 6, "text": "استخدام نتائج التقويم في خطط التحسين"}
            ]}
        ]
    },
    {
        "num": 3,
        "title": "التعليم والتعلم",
        "indicators_count": 8,
        "substandards": [
            {"title": "خصائص الخريجين ونواتج التعلم", "indicators": [
                {"num": 1, "text": "اتساق نواتج تعلم البرنامج مع الإطار الوطني للمؤهلات"},
                {"num": 2, "text": "قياس نواتج التعلم وتقييمها بشكل دوري"}
            ]},
            {"title": "المنهج الدراسي", "indicators": [
                {"num": 3, "text": "توازن المنهج وتغطيته لمتطلبات التخصص"},
                {"num": 4, "text": "توصيف المقررات والبرامج وفق نماذج المركز"}
            ]},
            {"title": "استراتيجيات التعليم والتعلم والتقويم", "indicators": [
                {"num": 5, "text": "فاعلية استراتيجيات التعليم والتعلم"},
                {"num": 6, "text": "عدالة وشفافية إجراءات تقييم الطلاب"}
            ]},
            {"title": "جودة التعليم والدعم التعليمي", "indicators": [
                {"num": 7, "text": "كفاءة الدعم التعليمي المقدم للطلاب"},
                {"num": 8, "text": "تنوع مصادر التعلم وتوفرها"}
            ]}
        ]
    },
    {
        "num": 4,
        "title": "الطلاب",
        "indicators_count": 7,
        "substandards": [
            {"title": "قبول وتسجيل الطلاب", "indicators": [
                {"num": 1, "text": "عدالة ووضوح ضوابط القبول والتحويل"},
                {"num": 2, "text": "كفاءة أنظمة التسجيل ورصد الدرجات"},
                {"num": 3, "text": "دقة وسرية سجلات الطلاب"}
            ]},
            {"title": "الخدمات والأنشطة الطلابية", "indicators": [
                {"num": 4, "text": "تفعيل الإرشاد الأكاديمي والطلابي"},
                {"num": 5, "text": "تنوع الأنشطة الطلابية ودعم المواهب"},
                {"num": 6, "text": "فاعلية نظام الشكاوى والتظلمات"},
                {"num": 7, "text": "قياس رضا الطلاب عن الخدمات المقدمة"}
            ]}
        ]
    },
    {
        "num": 5,
        "title": "هيئة التدريس",
        "indicators_count": 6,
        "substandards": [
            {"title": "استقطاب وتطوير هيئة التدريس", "indicators": [
                {"num": 1, "text": "كفاءة وتنوع تخصصات أعضاء هيئة التدريس"},
                {"num": 2, "text": "فاعلية برامج التطوير المهني لمنسوبي البرنامج"},
                {"num": 3, "text": "كفاية أعداد هيئة التدريس مقارنة بالطلاب"}
            ]},
            {"title": "حقوق وواجبات هيئة التدريس", "indicators": [
                {"num": 4, "text": "وضوح سياسات التقييم والترقية"},
                {"num": 5, "text": "التزام هيئة التدريس بأمانة العمل الأكاديمي"},
                {"num": 6, "text": "مشاركة هيئة التدريس في اللجان والأنشطة"}
            ]}
        ]
    },
    {
        "num": 6,
        "title": "مصادر التعلم والمرافق",
        "indicators_count": 5,
        "substandards": [
            {"title": "مصادر التعلم", "indicators": [
                {"num": 1, "text": "توفر المراجع والدوريات اللازمة للبرنامج"},
                {"num": 2, "text": "سهولة وصول الطلاب للهيئات والبيانات"},
                {"num": 3, "text": "تحديث المصادر التعليمية بانتظام"}
            ]},
            {"title": "المرافق والتجهيزات", "indicators": [
                {"num": 4, "text": "جودة القاعات الدراسية والمعامل"},
                {"num": 5, "text": "توفر معايير الأمن والسلامة في المرافق"}
            ]}
        ]
    },
    {
        "num": 6,
        "title": "مصادر التعلم والمرافق",
        "indicators_count": 5,
        "substandards": [
            {"title": "مصادر التعلم", "indicators": [
                {"num": 1, "text": "توفر المراجع والدوريات اللازمة للبرنامج"},
                {"num": 2, "text": "سهولة وصول الطلاب للهيئات والبيانات"},
                {"num": 3, "text": "تحديث المصادر التعليمية بانتظام"}
            ]},
            {"title": "المرافق والتجهيزات", "indicators": [
                {"num": 4, "text": "جودة القاعات الدراسية والمعامل"},
                {"num": 5, "text": "توفر معايير الأمن والسلامة في المرافق"}
            ]}
        ]
    },
    {
        "num": 7,
        "title": "البحث العلمي والابتكار",
        "indicators_count": 4,
        "substandards": [
            {"title": "البحث العلمي", "indicators": [
                {"num": 1, "text": "دعم البرنامج للإنتاج العلمي لهيئة التدريس"},
                {"num": 2, "text": "مشاركة الطلاب في الأنشطة البحثية"}
            ]},
            {"title": "الابتكار وخدمة المجتمع", "indicators": [
                {"num": 3, "text": "مساهمة البرنامج في حل المشكلات المجتمعية"},
                {"num": 4, "text": "تشجيع براءات الاختراع والابتكار"}
            ]}
        ]
    }
]

# Standard 6 was duplicated in my data, let's fix it
standards_data = standards_data[:-1] if standards_data[-1]["num"] == 7 else standards_data

all_standards_html = "\n".join([generate_standard(s["num"], s["title"], s["substandards"], s["indicators_count"]) for s in standards_data])

with open('resources/views/requests/stageThreeForm.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

start_marker = "        <!-- Standards Content Area -->\n        <div id=\"standards-container\">"
end_marker = "        </div>\n      </div><!-- Section 3: Independent Evaluations -->"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

if start_idx == -1 or end_idx == -1:
    print("Markers not found!")
    exit(1)

new_content = content[:start_idx] + "        <!-- Standards Content Area -->\n        <div id=\"standards-container\">" + all_standards_html + "\n        " + content[end_idx:]

with open('resources/views/requests/stageThreeForm.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Full replacement complete!")
